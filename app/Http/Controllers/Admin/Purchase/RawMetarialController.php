<?php

namespace App\Http\Controllers\Admin\Purchase;

use App\Http\Controllers\Controller;
use App\Http\Requests\RawMetrialPurchaseRequest;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\CodeService;
use App\Services\SupplierService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sudip\MediaUploader\Facades\MediaUploader;

class RawMetarialController extends Controller
{
    protected $type = "Purchase";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list purchase');
        $sql = Purchase::where('type','Raw')->orderBy('id', 'DESC')->with('items','items.product','items.unit','items.color','createdBy','updatedBy','supplier');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('challan_number', 'LIKE', '%'. $request->q.'%')
                ->orWhere('adjust_amount', 'LIKE', '%'. $request->q.'%')
                ->orWhere('date', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('items.product', function($q) use($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('createdBy', function($q) use($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->supplier_id) {
            $sql->where('supplier_id', $request->supplier_id);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        $suppliers = Supplier::where('status','Active')->get(['id', 'name']);


        return view('admin.purchase.rm', compact('result', 'suppliers'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add purchase');
        $items = [
            (object)[
                'id' => null,
                'product_id' => null,
                'unit_id' => null,
                'color_id' => null,
                'quantity' => null,
                'unit_price' => null,
                'total_price' => null
            ]
        ];

        $colors = Color::where('status','Active')->get();
        $suppliers = Supplier::where('status','Active')->get();
        $products = Product::with('unit')->whereIn('product_type', ['Fabric', 'Grey'])->where('status','Active')->get();

        return view('admin.purchase.rm', compact('items', 'products', 'suppliers', 'colors'))->with('create', 1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RawMetrialPurchaseRequest $request)
    {
        $this->authorize('add purchase');
        $imageName = '';
        if ($request->hasFile('challan_image')) {
            $file = MediaUploader::imageUpload($request->challan_image, 'challan', 1, null, [600, 600], [80, 80]);
            $imageName = $file['name'];
        }
        $code = CodeService::generate(Purchase::class, 'PRM', 'code');
        try {
            DB::beginTransaction();
            $storeData = [
                'challan_number' => $request->challan_number,
                'challan_image' => $imageName,
                'code' => $code,
                'supplier_id' => $request->supplier_id,
                'date' => dbDateFormat($request->date),
                'subtotal_amount' => $request->subtotal_amount,
                'total_amount' => $request->total_amount,
                'vat_percent' => $request->vat_percent ?? 0,
                'vat_amount' => $request->vat_amount ?? 0,
                'cost' => $request->cost ?? 0,
                'adjust_amount' => $request->adjust_amount ?? 0,
                'note' => $request->note,
                'type' => 'Raw',
                'created_by' => auth()->user()->id
            ];

            $data = Purchase::create($storeData);

            foreach ($request->product_id as $key => $row) {
                if($request->quantity[$key] > 0){
                    $product = Product::find($request->product_id[$key]);
                    $product->update([
                        'stock_price' => $request->unit_price[$key]
                    ]);
                    if ($request->cost) {
                        $cost = numberFormat( $request->cost/ $request->total_quantity, 2);
                        $actual_unit_price = numberFormat($request->unit_price[$key] + $cost, 2);
                    }else{
                        $cost = 0;
                        $actual_unit_price = $request->unit_price[$key];
                    }
                    ProductIn::create(
                        [
                            'type' => $this->type,
                            'flagable_id' => $data->id,
                            'flagable_type' => Purchase::class,
                            'product_id' => $request->product_id[$key],
                            'unit_id' => $request->unit_id[$key],
                            'color_id' => $request->color_id[$key],
                            'quantity' => $request->quantity[$key],
                            'unit_price' => $request->unit_price[$key],
                            'total_price' => $request->amount[$key],
                            'cost' => $cost,
                            'used_quantity' => 0,
                            'actual_unit_price' => $actual_unit_price,
                            'created_at' => now(),
                        ]
                    );
                }
            }
            if ($data->adjust_amount > 0) {
                SupplierService::stockAdjustment($data);
            }
            DB::commit();
            $request->session()->flash('successMessage', 'Raw Metarial was successfully added!');
            return redirect()->route('admin.purchase.raw.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.purchase.raw.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $this->authorize('show purchase');
        $data = Purchase::with('items','createdBy','updatedBy','supplier')->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.purchase.raw.index', qArray());
        }

        return view('admin.purchase.rm', compact('data'))->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit purchase');
        $data = Purchase::with('items','createdBy','updatedBy','supplier')->find($id);
        $items = $data->items;
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.purchase.raw.index', qArray());
        } else {
            //check used quantity
            foreach($data->items as $item){
                if($item->used_quantity > 0){
                    $request->session()->flash('errorMessage', 'This Raw items already consumed !!');
                    return redirect()->route('admin.purchase.raw.index', qArray());
                }
            }
        }
        $colors = Color::where('status','Active')->get();
        $units = Unit::where('status','Active')->get();
        $suppliers = Supplier::where('status','Active')->get();
        $products = Product::with('unit')->whereIn('product_type', ['Fabric', 'Grey'])->where('status','Active')->get();
        return view('admin.purchase.rm', compact('items', 'products', 'suppliers', 'units', 'colors', 'data'))->with('edit', $id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RawMetrialPurchaseRequest $request, $id)
    {
        $this->authorize('edit purchase');
        $data = Purchase::with('items')->find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Raw not found!');
            return redirect()->route('admin.purchase.raw.index', qArray());
        }

        //check items in used
        foreach($data->items as $item){
            if($item->used_quantity > 0){
                $request->session()->flash('errorMessage', 'This Raw items already consumed !!');
                return redirect()->route('admin.purchase.raw.index', qArray());
            }
        }

        try {
            DB::beginTransaction();

            $storeData = [
                'challan_number' => $request->challan_number,
                'supplier_id' => $request->supplier_id,
                'date' => dbDateFormat($request->date),
                'subtotal_amount' => $request->subtotal_amount,
                'total_amount' => $request->total_amount,
                'vat_percent' => $request->vat_percent ?? 0,
                'vat_amount' => $request->vat_amount ?? 0,
                'cost' => $request->cost ?? 0,
                'adjust_amount' => $request->adjust_amount ?? 0,
                'note' => $request->note,
                'type' => 'Raw',
                'updated_by' => auth()->user()->id
            ];

            if ($request->hasFile('challan_image')) {
                if($data->challan_image) {
                    MediaUploader::delete('challan', $data->challan_image, true);
                }
                $file = MediaUploader::imageUpload($request->challan_image, 'challan', 1, null, [600, 600], [80, 80]);
                $storeData['challan_image'] = $file['name'];
            }
            $data->update($storeData);

            if ($data && count($request->product_id) > 0 ) {
                 ProductIn::whereNotIn('product_id', $request->product_id)->whereNotIn('unit_id', $request->unit_id)->whereNotIn('color_id', $request->color_id)->where('flagable_id', $data->id)->where('flagable_type', Purchase::class)->delete();      
                foreach ($request->product_id as $key => $product) {
                    $product = Product::find($request->product_id[$key]);
                    $product->update([
                        'stock' => $request->unit_price[$key]
                    ]);
    
                    if ($request->cost > 0) {
                        $cost = numberFormat( $request->cost / $request->total_quantity, 2);
                        $actual_unit_price = numberFormat($request->unit_price[$key] + $cost, 2);
                    }else{
                        $cost = 0;
                        $actual_unit_price = $request->unit_price[$key];
                    }
                    $updateItemData = [
                        'type' => $this->type,
                        'flagable_id' => $data->id,
                        'flagable_type' => Purchase::class,
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key],
                        'color_id' => $request->color_id[$key],
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total_price' => $request->amount[$key],
                        'cost' => $cost,
                        'used_quantity' => 0,
                        'actual_unit_price' => $actual_unit_price,
                        'updated_at' => now(),
                    ];
                   $item = ProductIn::where('product_id', $request->product_id[$key])->where('unit_id', $request->unit_id[$key])->where('color_id', $request->color_id[$key])->where('flagable_id', $data->id)->where('flagable_type', Purchase::class)->first();
                   if ($item) {
                        $item->update($updateItemData);
                   }else {
                        ProductIn::create($updateItemData);
                   }
                }
            }
            if ($data->adjust_amount > 0) {
                SupplierService::stockAdjustment($data, $data->id);
            }
            DB::commit();

            $request->session()->flash('successMessage', 'Raw was successfully updated!');
            return redirect()->route('admin.purchase.raw.index');

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.purchase.raw.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete purchase');
        $data = Purchase::with('items')->find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.purchase.raw.index', qArray());
        }

        foreach($data->items as $item){
            if($item->used_quantity > 0){
                $request->session()->flash('errorMessage', 'This Raw items already consumed !!');
                return redirect()->route('admin.purchase.raw.index', qArray());
            }
            $item->delete();
        }
        if($data->challan_image) {
            MediaUploader::delete('challan', $data->challan_image, true);
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Raw was successfully deleted!');
        return redirect()->route('admin.purchase.raw.index', qArray());
    }
    public function getSupplierPurchase(Request $request)
    {

        $sql = Purchase::with('items')->select('id','date','code')->where('supplier_id', $request->id);
        if ($request->type) {
            if($request->type == 'Finished') {
                $sql->whereIn('type' , ['Finished', 'Turkey']);
            } else{
                $sql->where('type' , $request->type);
            }
        }
        $data = $sql->get();
        
        return response()->json(['success' => true, 'data' => $data]);

    }
    public function getPurchase(Request $request)
    {
        $data = Purchase::with(['items.product' , 'items.unit', 'items.color'])->find($request->id);
        return response()->json(['success' => true, 'data' => $data]);

    }
}
