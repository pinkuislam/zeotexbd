<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderBaseProductionRequest;
use App\Models\Color;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\Production;
use App\Models\ProductOut;
use App\Models\ProductUse;
use App\Services\CodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderBaseProductionController extends Controller
{

    protected $type = "Production";

    public function index(Request $request)
    {
        $this->authorize('list production');
        $data = Production::with('raw_items', 'prod_items', 'raw_items.product', 'prod_items.product')->orderBy('created_at', 'DESC');

        if ($request->q) {
            $data->where(function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('note', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('date', 'LIKE', '%' . $request->q . '%');
            });
            $data->orwhereHas('raw_items.product', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $data->orwhereHas('prod_items.product', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->from) {
            $data->where('productions.date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $data->where('productions.date', '<=', dbDateFormat($request->to));
        }
        $prods = $data->paginate($request->limit ?? config('settings.per_page_limit'));
        return view('admin.production.index', compact('prods'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add production');
        $prod_items = [
            (object)[
                'id' => null,
                'product_id' => null,
                'color_id' => null,
                'quantity' => null,
            ]
        ];
        $products = Product::with(['unit', 'item'])->whereIn('product_type', ['Base'])->where('status', 'Active')->get();
        $colors = Color::where('status', 'Active')->get();
        return view('admin.production.index', compact('products', 'colors', 'prod_items'))->with('order', 1);
    }

    public function store(OrderBaseProductionRequest $request)
    {
        $this->authorize('add production');
        DB::beginTransaction();
        try {
            $order = Order::find($request->order_id);
            $hasAlreadyStock = true;
            foreach ($request->product_id as $key => $row) {
                if ($request->fabric_quantity[$key] > 0) {
                    $hasAlreadyStock = false;
                }
            }

            if ($hasAlreadyStock) {
                $order->update(['has_stock_done' => 'Yes']);
            } else {
                $code = CodeService::generate(Production::class, 'PR', 'code');

                $storeData = [
                    'code' => $code,
                    'order_id' => $order->id,
                    'order_code' => $order->code,
                    'date' => dbDateFormat($request->date),
                    'note' => $request->note,
                    'created_by' => auth()->user()->id
                ];

                $data = Production::create($storeData);
                $order->update(['has_stock_done' => 'Yes']);
                if ($data && count($request->product_id) > 0) {
                    foreach ($request->product_id as $key => $row) {
                        if ($request->fabric_quantity[$key] > 0) {
                            $product = Product::where('id', $request->product_id[$key])->first();
                            $cost = $request->fabric_quantity[$key]  * $request->fabric_unit_price[$key];
                            $itemData = [
                                'type' => $this->type,
                                'flagable_id' => $data->id,
                                'flagable_type' => Production::class,
                                'product_id' => $request->product_id[$key],
                                'unit_id' => $product->unit_id,
                                'color_id' => $request->color_id[$key],
                                'quantity' => $request->quantity[$key] ?? 0,
                                'unit_price' => $cost / $request->quantity[$key],
                                'total_price' => $cost,
                                'used_quantity' => 0,
                                'status' => 0,
                                'created_at' => now(),
                            ];
                            ProductIn::create($itemData);

                            //raw material outs
                            $itemDataProd = [
                                'type' => $this->type,
                                'flagable_id' => $data->id,
                                'flagable_type' => Production::class,
                                'product_id' => $request->fabric_product_id[$key],
                                'unit_id' => $request->fabric_unit_id[$key],
                                'color_id' => $request->color_id[$key],
                                'quantity' => $request->fabric_quantity[$key],
                                'unit_price' => $request->fabric_unit_price[$key],
                                'total_price' => $request->fabric_unit_price[$key] * $request->fabric_quantity[$key],
                                'created_at' => now(),
                            ];
                            $outproduct = ProductOut::create($itemDataProd);

                            $inproducts =  ProductIn::where('product_id', $request->fabric_product_id[$key])
                                ->where('color_id', $request->color_id[$key])
                                ->where('status', 0)->get();
                            $rawQty = $request->fabric_quantity[$key];

                            foreach ($inproducts as $inproduct) {
                                if ($rawQty > 0) {
                                    if (($inproduct->quantity - $inproduct->used_quantity) < $rawQty) {

                                        $rawQty -= ($inproduct->quantity - $inproduct->used_quantity);
                                        ProductUse::create([
                                            'product_in_id' => $inproduct->id,
                                            'product_out_id' => $outproduct->id,
                                            'quantity' => $inproduct->quantity - $inproduct->used_quantity,
                                        ]);

                                        $inproduct->update([
                                            'used_quantity' => $inproduct->quantity,
                                            'status' => 1
                                        ]);
                                    } else if (($inproduct->quantity - $inproduct->used_quantity) > $rawQty) {
                                        $inproduct->update([
                                            'used_quantity' => $rawQty + $inproduct->used_quantity,
                                            'status' => 0
                                        ]);
                                        ProductUse::create([
                                            'product_in_id' => $inproduct->id,
                                            'product_out_id' => $outproduct->id,
                                            'quantity' => $rawQty,
                                        ]);
                                        $rawQty = 0;
                                    } else {
                                        $inproduct->update([
                                            'used_quantity' => $inproduct->quantity,
                                            'status' => 1
                                        ]);
                                        ProductUse::create([
                                            'product_in_id' => $inproduct->id,
                                            'product_out_id' => $outproduct->id,
                                            'quantity' => $rawQty,
                                        ]);
                                        $rawQty = 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
            $request->session()->flash('successMessage', 'Production was successfully added!');
            return redirect()->route('admin.production.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.production.index');
        }
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show production');
        $data = Production::with('raw_items', 'prod_items', 'raw_items.product', 'prod_items.product')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.production.index', qArray());
        }

        return view('admin.production.index', compact('data'))->with('show', $id);
    }

    public function destroy(Request $request, $id)
    {

        $data = Production::find($id);

        DB::beginTransaction();
        try{

            $productionUsed = ProductUse::where('product_outs.flagable_id', $id)->where('product_outs.flagable_type', Production::class)
            ->select('product_uses.*')
            ->join('product_outs', 'product_uses.product_out_id', '=', 'product_outs.id')
            ->get();
            foreach($productionUsed as $value){
                $info = ProductIn::find($value->product_in_id);
                ProductIn::where('id', $value->product_in_id)->update(['used_quantity' => ($info->used_quantity - $value->quantity), 'status'=> 0 ]);
            }

            $usesIds = $productionUsed->pluck('id');
            ProductUse::whereIn('id', $usesIds)->delete();
            ProductOut::where('flagable_id', $data->id)->where('flagable_type', Production::class)->delete();
            ProductIn::where('flagable_id', $data->id)->where('flagable_type', Production::class)->delete();
            $data->delete();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Production was not deleted! ' . $e);
            return redirect()->route('admin.production.index', qArray());
        }

        $request->session()->flash('successMessage', 'Production was successfully deleted!');
        return redirect()->route('admin.production.order-base.index', qArray());
    }

    public function getRawStock(Request $request)
    {
        $product = Product::with('item', 'items')->find($request->product_id);
        //dd($product);
        try {
            $fabricstock = ProductIn::where('product_id', $product->item->fabric_product_id)->where('color_id', $request->color_id)
                ->selectRaw('sum(quantity) as total_qty, sum(used_quantity) as total_used_qty, (sum(quantity)-sum(used_quantity)) as totalstock')
                ->first();
            $stock = $fabricstock->totalstock;
            $totalQty = 0;
            $fabric_unit = '';
            $unit = '';
            if ($product->product_type == 'Combo') {
                foreach ($product->items as $key => $item) {
                    $baseProduct = Product::with('item')->find($item->base_id);
                    $totalQty += ($item->quantity * $baseProduct->item->fabric_quantity);
                    $fabric_unit = $baseProduct->item->fabricUnit ? $baseProduct->item->fabricUnit->name : '';
                    $unit = $baseProduct->unit ?  $baseProduct->unit->name : '';
                }
            } else {
                $totalQty +=  $product->item->fabric_quantity;
                $fabric_unit = $product->item->fabricUnit ? $product->item->fabricUnit->name : '';
                $unit = $product->unit ?  $product->unit->name : '';
            }
            return response()->json(['success' => true, 'fabric_product_id' => $product->item->fabric_product_id, 'stock' => $stock, 'fabric_quantity' => $totalQty, 'fabric_unit' => $fabric_unit, 'unit' => $unit]);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getOrder(Request $request)
    {
        $order = Order::with([
            'items' => function ($q) {
                $q->whereHas('productFabric.fabric', function ($q) {
                    $q->where('name', 'NOT LIKE', '%' . 'Turkey' . '%');
                });
                $q->with([
                    'product' => function($q) {
                        $q->select(['id', 'name', 'product_type', 'stock_price']);
                        $q->withStock();
                    },
                    'productBases:product_id,base_id,quantity',
                    'productBases.product' => function ($q) {
                        $q->select(['id', 'name', 'stock_price']);
                        $q->withStock();
                    },
                    'productBases.productFabric',
                    'productBases.productFabric.fabric' => function ($q) {
                        $q->select(['id', 'name', 'stock_price']);
                        $q->withStock();
                    },
                    'productBases.productFabric.fabricUnit:id,name',
                    'unit',
                    'color',
                    'productFabric.fabric',
                    'productFabric.fabricUnit:id,name',
                    'productFabric.fabric' => function ($q) {
                        $q->select(['id', 'name', 'stock_price']);
                        $q->withStock();
                    },
                ]);
            },
        ])
            ->where('code', $request->input('code'))
            ->where('status', 'Ordered')
            ->whereIn('has_stock_done', ['No', 'Turkey'])
            ->first();
        if ($order) {
            return response()->json(['success' => true, 'data' => $order]);
        } else {
            return response()->json(['success' => false, 'data' => 'No data found!']);
        }
    }
}
