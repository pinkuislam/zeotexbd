<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionRequest;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\Production;
use App\Models\ProductOut;
use App\Models\ProductUse;
use App\Services\CodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller {

    protected $type = "Production";

    public function index(Request $request)
    {
        $data = Production::with('raw_items','prod_items', 'raw_items.product','prod_items.product')->orderBy('created_at','DESC');

        if ($request->q) {
            $data->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('note', 'LIKE', '%'. $request->q.'%')
                ->orWhere('date', 'LIKE', '%'. $request->q.'%');
            });
            $data->orwhereHas('raw_items.product', function($q) use($request) {
                $q->where('name', $request->q);
            });
            $data->orwhereHas('prod_items.product', function($q) use($request) {
                $q->where('name', $request->q);
            });
        }

        if ($request->from) {
            $data->where('productions.date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $data->where('productions.date', '<=', dbDateFormat($request->to));
        }
        $prods = $data->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.production.production',compact('prods'))->with('list', 1);
    }

    public function create()
    {
        $prod_items = [
            (object)[
                'id' => null,
                'product_id' => null,
                'color_id' => null,
                'quantity' => null,
            ]
        ];
        $products = Product::with(['unit'])->where('product_type', 'Base-Ready-Production')->where('status','Active')->get();
        $colors = Color::where('status','Active')->get();
        return view('admin.production.production', compact('products', 'colors','prod_items'))->with('create', 1);
    }

    public function store(ProductionRequest $request)
    {
        DB::beginTransaction();
        try {
            $code = CodeService::generate(Production::class, 'PR', 'code');
            $storeData = [
                'code' => $code,
                'date' => dbDateFormat($request->date),
                'note' => $request->note,
                'created_by' => auth()->user()->id
            ];

            $data = Production::create($storeData);

            if ($data && count($request->product_id) > 0 ) {
                foreach ($request->product_id as $key => $row) {
                    if ($request->stock[$key] >= $request->quantity[$key]) {
                        $product = Product::where('id',$request->product_id[$key])->first();
                        $itemData = [
                            'type' => $this->type,
                            'flagable_id' => $data->id,
                            'flagable_type' => Production::class,
                            'product_id' => $request->product_id[$key],
                            'unit_id' => $product->unit_id,
                            'color_id' => $request->color_id[$key],
                            'quantity' => $request->quantity[$key] ?? 0,
                            'unit_price' => $product->unit_price ?? 0,
                            'total_price' => $request->quantity[$key] * $product->unit_price ?? 0,
                            'used_quantity' => 0,
                            'status' => 0,
                            'created_at' => now(),
                        ];
                        ProductIn::create($itemData);
                        $rawQty = $request->quantity[$key] * $product->item->fabric_quantity;
                        //raw material outs
                        $itemDataProd = [
                            'type' => $this->type,
                            'flagable_id' => $data->id,
                            'flagable_type' => Production::class,
                            'product_id' => $product->item->fabric_product_id,
                            'unit_id' => $product->item->fabric_unit_id ,
                            'color_id' => $request->color_id[$key] ,
                            'quantity' => $rawQty ?? 0,
                            'unit_price' => $product->unit_price ?? 0,
                            'total_price' => $rawQty * $product->unit_price ?? 0,
                            'created_at' => now(),
                        ];
                        $outproduct = ProductOut::create($itemDataProd);
                        $inproducts =  ProductIn::where('product_id', $product->item->fabric_product_id)
                        ->where('color_id', $request->color_id[$key])
                        ->where('status', 0)->get();
                        foreach($inproducts as $inproduct){
                            if($rawQty > 0){
                                if(($inproduct->quantity - $inproduct->used_quantity) < $rawQty){
    
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
    
                                    
                                } else if(($inproduct->quantity - $inproduct->used_quantity) > $rawQty){
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
            DB::commit();
            $request->session()->flash('successMessage', 'Production was successfully added!');
            return redirect()->route('admin.production.order-base.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.production.order-base.index');
        }
    }

    public function show($id)
    {
        $data = Production::findOrFail($id);

        return view('admin.production.production', compact('data'))->with('show', $id);
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

    public function getRawStock(Request $request){
        $product = Product::with('item')->find($request->product_id);
        try {
            $fabricstock = ProductIn::where('product_id',$product->item->fabric_product_id)->where('color_id',$request->color_id)
            ->selectRaw('sum(quantity) as total_qty, sum(used_quantity) as total_used_qty, (sum(quantity)-sum(used_quantity)) as totalstock')
            ->first();
            $stock = $fabricstock->totalstock;
            $totalQty = 0;
            $fabric_unit = '';
            $unit = '';
                $totalQty +=  $product->item->fabric_quantity;
                $fabric_unit =$product->item->fabricUnit ? $product->item->fabricUnit->name : '';
                $unit = $product->unit ?  $product->unit->name : '';
            return response()->json(['success' => true, 'stock' =>$stock, 'fabric_quantity' =>$totalQty, 'fabric_unit' => $fabric_unit , 'unit' => $unit ]);
        } catch (Exception $e) {
           return $e;
        }

    }
}