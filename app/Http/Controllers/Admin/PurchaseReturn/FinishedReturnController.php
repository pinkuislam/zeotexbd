<?php

namespace App\Http\Controllers\Admin\PurchaseReturn;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\ProductUse;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Services\CodeService;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class FinishedReturnController extends Controller
{
    protected $type = "PurchaseReturn";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list purchase-return');
        $sql = PurchaseReturn::where('type', 'Finished')
        ->orderBy('id', 'DESC')
        ->with([
            'items',
            'items.product',
            'createdBy',
            'updatedBy'
        ]);

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('total_amount', 'LIKE', '%'. $request->q.'%')
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

        $suppliers = Supplier::where('status','Active')->get();


        return view('admin.purchase.return.finished', compact('result', 'suppliers'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add purchase-return');

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
        $suppliers = Supplier::where('status', 'Active')->get();
        $colors = Color::where('status','Active')->get();
        $products = Product::with('unit')->whereIn('product_type', [ 'Base', 'Product', 'Combo'])->where('status','Active')->get();

        return view('admin.purchase.return.finished', compact('suppliers', 'items', 'products', 'colors'))->with('create', 1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add purchase-return');
        try {
            $credentials = $request->except('_token');
            $validator = Validator::make($credentials, [
                'supplier_id' => 'required|integer',
                'product_id' => 'required|array|min:1',
                'product_id.*' => 'required|integer',
                'unit_id' => 'required|array|min:1',
                'unit_id.*' => 'required|integer',
                'color_id' => 'nullable|array',
                'color_id.*' => 'nullable|integer',
                'quantity' => 'nullable|array',
                'quantity.*' => 'nullable|numeric',
                'unit_price' => 'nullable|array',
                'unit_price.*' => 'nullable|numeric',
                'amount' => 'nullable|array',
                'amount.*' => 'nullable|numeric',
                'subtotal_amount' => 'required|numeric',
                'cost' => 'nullable|numeric',
                'total_amount' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => implode(", " , $validator->messages()->all())], 200);
            }
 
        $code = CodeService::generate(PurchaseReturn::class, 'PRMR', 'code');
        DB::beginTransaction();
        $storeData = [
            'code' => $code,
            'supplier_id' => $request->supplier_id,
            'date' => dbDateFormat($request->date),
            'subtotal_amount' => $request->subtotal_amount,
            'total_amount' => $request->total_amount,
            'cost' => $request->cost ?? 0,
            'note' => $request->note,
            'type' => 'Finished',
            'created_by' => auth()->user()->id
        ];

        $data = PurchaseReturn::create($storeData);

        foreach ($request->product_id as $key => $row) {
            if( array_sum($request->quantity) > 0 && $request->stock[$key] >= $request->quantity[$key]){
                $FinishedQty = $request->quantity[$key] ?? 0;
                $outproduct = ProductOut::create(
                    [
                        'type' => $this->type,
                        'flagable_id' => $data->id,
                        'flagable_type' => PurchaseReturn::class,
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key],
                        'color_id' => $request->color_id[$key],
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total_price' => ($request->quantity[$key] * $request->unit_price[$key]),
                        'created_at' => now(),
                    ]
                );
                $inproducts =  ProductIn::where('product_id', $request->product_id[$key])
                    ->where('color_id', $request->color_id[$key])
                    ->where('status', 0)->get();
                    foreach($inproducts as $inproduct){
                        if($FinishedQty > 0){
                            if(($inproduct->quantity - $inproduct->used_quantity) < $FinishedQty){

                                $FinishedQty -= ($inproduct->quantity - $inproduct->used_quantity);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $inproduct->quantity - $inproduct->used_quantity,
                                ]);

                                $inproduct->update([
                                    'used_quantity' => $inproduct->quantity,
                                    'status' => 1
                                ]);

                                
                            } else if(($inproduct->quantity - $inproduct->used_quantity) > $FinishedQty){
                                $inproduct->update([
                                    'used_quantity' => $FinishedQty + $inproduct->used_quantity,
                                    'status' => 0
                                ]);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $FinishedQty,
                                ]);
                                $FinishedQty = 0;
                            } else {
                                $inproduct->update([
                                    'used_quantity' => $inproduct->quantity,
                                    'status' => 1
                                ]);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $FinishedQty,
                                ]);
                                $FinishedQty = 0;
                            }

                        }
                            
                    }
            }
        }
        DB::commit();
        $request->session()->flash('successMessage', 'Finished Return was successfully added!');
        return redirect()->route('admin.purchase-return.finished.index');
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
        $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
        return redirect()->route('admin.purchase-return.finished.index');
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
        $this->authorize('show purchase-return');
        $data = PurchaseReturn::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.purchase-return.finished.index', qArray());
        }

        return view('admin.purchase.return.finished', compact('data'))->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $this->authorize('edit purchase-return');

        $data = PurchaseReturn::with('items')->findOrFail($id);
        if (count($data->items) > 0) {
            $items = $data->items;
        }else {
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
        }
        $suppliers = Supplier::where('status','Active')->get();
        $colors = Color::where('status','Active')->get();
        $products = Product::with('unit')->whereIn('product_type', [ 'Base', 'Product', 'Combo'])->where('status','Active')->get();
        return view('admin.purchase.return.finished', compact('items', 'suppliers', 'data','products', 'colors'))->with('edit', $id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('edit purchase-return');
        $data = PurchaseReturn::with('items')->find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Finished Return not found!');
            return redirect()->route('admin.purchase-return.finished.index', qArray());
        }

        $credentials = $request->except('_token');
        $validator = Validator::make($credentials, [
            'supplier_id' => 'required|integer',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|integer',
            'color_id' => 'nullable|array',
            'color_id.*' => 'nullable|integer',
            'quantity' => 'nullable|array',
            'quantity.*' => 'nullable|numeric',
            'unit_price' => 'nullable|array',
            'unit_price.*' => 'nullable|numeric',
            'amount' => 'nullable|array',
            'amount.*' => 'nullable|numeric',
            'subtotal_amount' => 'required|numeric',
            'cost' => 'nullable|numeric',
            'total_amount' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(", " , $validator->messages()->all())], 200);
        }

        try {
            DB::beginTransaction();

            $storeData = [
                'supplier_id' => $request->supplier_id,
                'date' => dbDateFormat($request->date),
                'subtotal_amount' => $request->subtotal_amount,
                'total_amount' => $request->total_amount,
                'cost' => $request->cost ?? 0,
                'note' => $request->note,
                'type' => 'Finished',
                'updated_by' => auth()->user()->id
            ];
            $data->update($storeData);
            $productionUsed = ProductUse::where('product_outs.flagable_id', $id)->where('product_outs.flagable_type', PurchaseReturn::class)
            ->select('product_uses.*')
            ->join('product_outs', 'product_uses.product_out_id', '=', 'product_outs.id')
            ->get();
            foreach($productionUsed as $value){
                $info = ProductIn::find($value->product_in_id);
                ProductIn::where('id', $value->product_in_id)->update(['used_quantity' => ($info->used_quantity - $value->quantity), 'status'=> 0 ]);
            }
            $usesIds = $productionUsed->pluck('id');
            ProductUse::whereIn('id', $usesIds)->delete();
            ProductOut::where('flagable_id', $data->id)->where('flagable_type', PurchaseReturn::class)->delete();
            foreach ($request->product_id as $key => $row) {
                if( array_sum($request->quantity) > 0 && $request->stock[$key] >= $request->quantity[$key]){
                    $FinishedQty = $request->quantity[$key] ?? 0;
                    $outproduct = ProductOut::create(
                        [
                            'type' => $this->type,
                            'flagable_id' => $data->id,
                            'flagable_type' => PurchaseReturn::class,
                            'product_id' => $request->product_id[$key],
                            'unit_id' => $request->unit_id[$key],
                            'color_id' => $request->color_id[$key],
                            'quantity' => $request->quantity[$key],
                            'unit_price' => $request->unit_price[$key],
                            'total_price' => $request->quantity[$key] * $request->unit_price[$key],
                            'created_at' => now(),
                        ]
                    );
                    $inproducts =  ProductIn::where('product_id', $request->product_id[$key])
                    ->where('color_id', $request->color_id[$key])
                    ->where('status', 0)->get();
                    foreach($inproducts as $inproduct){
                        if($FinishedQty > 0){
                            if(($inproduct->quantity - $inproduct->used_quantity) < $FinishedQty){

                                $FinishedQty -= ($inproduct->quantity - $inproduct->used_quantity);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $inproduct->quantity - $inproduct->used_quantity,
                                ]);

                                $inproduct->update([
                                    'used_quantity' => $inproduct->quantity,
                                    'status' => 1
                                ]);

                                
                            } else if(($inproduct->quantity - $inproduct->used_quantity) > $FinishedQty){
                                $inproduct->update([
                                    'used_quantity' => $FinishedQty + $inproduct->used_quantity,
                                    'status' => 0
                                ]);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $FinishedQty,
                                ]);
                                $FinishedQty = 0;
                            } else {
                                $inproduct->update([
                                    'used_quantity' => $inproduct->quantity,
                                    'status' => 1
                                ]);
                                ProductUse::create([
                                    'product_in_id' => $inproduct->id,
                                    'product_out_id' => $outproduct->id,
                                    'quantity' => $FinishedQty,
                                ]);
                                $FinishedQty = 0;
                            }

                        }
                            
                    }
                }
            }
            DB::commit();

            $request->session()->flash('successMessage', 'Finished Return was successfully updated!');
            return redirect()->route('admin.purchase-return.finished.index');

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.purchase-return.finished.index');
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
        $this->authorize('delete purchase-return');
        $data = PurchaseReturn::with('items')->find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.purchase-return.finished.index', qArray());
        }

        DB::beginTransaction();
        try{

            $purchaseReturnnUsed = ProductUse::where('product_outs.flagable_id', $id)->where('product_outs.flagable_type', PurchaseReturn::class)
            ->select('product_uses.*')
            ->join('product_outs', 'product_uses.product_out_id', '=', 'product_outs.id')
            ->get();
            foreach($purchaseReturnnUsed as $value){
                $info = ProductIn::find($value->product_in_id);
                ProductIn::where('id', $value->product_in_id)->update(['used_quantity' => ($info->used_quantity - $value->quantity), 'status'=> 0 ]);
            }

            $usesIds = $purchaseReturnnUsed->pluck('id');
            ProductUse::whereIn('id', $usesIds)->delete();
            ProductOut::where('flagable_id', $data->id)->where('flagable_type', PurchaseReturn::class)->delete();
            $data->delete();
            DB::commit();
            $request->session()->flash('successMessage', 'Finished Return was successfully deleted!');
            return redirect()->route('admin.purchase-return.finished.index', qArray());
        }catch (\Exception $e){
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Finished Return was not deleted! ' . $e);
            return redirect()->route('admin.purchase-return.finished.index', qArray());
        }
    }
    public function getProductStock(Request $request)
    {
        $data = ProductIn::where('product_id', $request->product_id)->where('color_id', $request->color_id)
            ->selectRaw('(sum(quantity)-sum(used_quantity)) as totalstock')
            ->first();
        return $data->totalstock ?? 0;
    }
}
