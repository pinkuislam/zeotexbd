<?php

namespace App\Http\Controllers\Admin\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleReturnRequest;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\ProductUse;
use App\Models\SaleReturn;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    protected $type = "SaleReturn";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list sale-return');
        if (auth()->user()->hasRole('Super Admin')) {
            $sql = SaleReturn::orderBy('id', 'DESC')->with('items','items.product','items.unit','items.color','createdBy','updatedBy','user','customer','resellerBusiness','sale');
        }else{
            $sql = SaleReturn::orderBy('id', 'DESC')->with('items','items.product','items.unit','items.color','createdBy','updatedBy','user','customer','resellerBusiness','sale')->where('created_by',auth()->user()->id);
        }
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('return_amount', 'LIKE', '%'. $request->q.'%')
                ->orWhere('date', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('customer', function($q) use($request) {
                $q->where('name', $request->q);
                $q->orWhere('mobile', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('items.product', function($q) use($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('resellerBusiness', function($q) use($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('mobile', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('user', function($q) use($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('createdBy', function($q) use($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->customer_id) {
            $sql->where('customer_id', $request->customer_id);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $result = $sql->get();

        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            $customers = Customer::where('status','Active')->get(['id','name','mobile']);
        }else{
            $customers = Customer::where('status','Active')->where('type',auth()->user()->role)->get(['id','name','mobile']);
        }
        
        return view('admin.sale.sale_return', compact('result', 'customers'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add sale-return');
        $data['code'] = ''; 
        return view('admin.sale.sale_return', $data)->with('create', 1);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaleReturnRequest $request)
    {
        $this->authorize('add sale-return');
        DB::beginTransaction();
        try {
            
            $data = Sale::find($request->sale_id);
            if ($data) {
                $updateData = [
                    'has_return' => 'Yes',
                    'updated_by' => auth()->user()->id
                ];
                $data->update($updateData);
                $storeData = [
                    'code' => $request->code,
                    'date' => dbDateFormat($request->date),
                    'user_id' => $request->user_id ?? auth()->user()->id,
                    'sale_id' => $request->sale_id,
                    'customer_id' => $request->customer_id,
                    'reseller_business_id' => $request->reseller_business_id,
                    'return_amount' => $request->total_amount,
                    'note' => $request->note,
                    'cost' => $request->cost,
                    'deduction_amount' => $request->deduction_amount,
                    'reseller_amount' => $request->reseller_amount,
                    'created_by' => auth()->user()->id
                ];
                $data = SaleReturn::create($storeData);
                if( array_sum($request->quantity) > 0){
                    foreach ($request->product_id as $key => $row) {
                        if($request->quantity[$key]){
                            $productOut = ProductOut::findOrFail($request->product_out_id[$key]);
                            $productUses = ProductUse::where('product_out_id', $productOut->id)->get();
                            
                            $qty = $request->quantity[$key];
                            foreach($productUses as $use) {
                                $useQty = $use->quantity;
                                if($qty > $useQty){
                                    $use->productIn->update(['return_quantity' => $use->productIn->return_quantity + $useQty]);
                                    $qty = $qty - $useQty;
                                } else {
                                    $use->productIn->update(['return_quantity' => $use->productIn->return_quantity + $qty]);
                                    break;
                                }
                            }
                            $storeDataIn = [
                                'type' => $this->type,
                                'flagable_id' => $data->id,
                                'flagable_type' => SaleReturn::class,
                                'product_id' => $request->product_id[$key],
                                'unit_id' => $request->unit_id[$key] ,
                                'color_id' => $request->color_id[$key] ,
                                'quantity' => $request->quantity[$key],
                                'unit_price' => $request->unit_price[$key],
                                'total_price' => $request->unit_price[$key] * $request->quantity[$key],
                                'created_at' => now(),
                            ];
                            ProductIn::create($storeDataIn);
                        }
                        
                    }

                }
            }
            DB::commit();
            $request->session()->flash('successMessage', 'Sale Return was successfully added!');
            return redirect()->route('admin.sale.return.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return back();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('show sale-return');
        $data['data'] = SaleReturn::with('items','items.product','items.unit','items.color','createdBy','updatedBy','user','customer','resellerBusiness','sale')->find($id);
        return view('admin.sale.sale_return', $data)->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $this->authorize('edit sale-return');
        $data['data'] = SaleReturn::with([
            'items',
            'items.product',
            'items.unit',
            'items.color',
            'createdBy',
            'updatedBy',
            'user',
            'customer',
            'resellerBusiness',
            'sale'
        ])->find($id);
        $sql = Sale::with([
            'items',
            'items.product',
            'items.unit',
            'items.color',
            'createdBy',
            'updatedBy',
            'user',
            'customer',
            'resellerBusiness',
            'saleReturn',
        ]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('user_id', auth()->user()->id);
        }
        $data['sale'] = $sql->where('code',$data['data']->sale->code)->first();
        $data['code'] = $data['data']->code;
        return view('admin.sale.sale_return', $data)->with('edit', $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaleReturnRequest $request,$id)
    {
        $this->authorize('edit sale-return');
        $data = SaleReturn::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return back();
        }
        try {
            DB::beginTransaction();
            $updateData = [
                'date' => dbDateFormat($request->date),
                'user_id' => $request->user_id ?? auth()->user()->id,
                'sale_id' => $request->sale_id,
                'customer_id' => $request->customer_id,
                'reseller_business_id' => $request->reseller_business_id,
                'return_amount' => $request->total_amount,
                'note' => $request->note,
                'cost' => $request->cost,
                'deduction_amount' => $request->deduction_amount,
                'reseller_amount' => $request->reseller_amount,
                'updated_by' => auth()->user()->id
            ];
            $data->update($updateData);
            $data->items->each->delete();
            if( array_sum($request->quantity) > 0){
                foreach ($request->product_id as $key => $row) {
                    $storeDataIn = [
                        'type' => $this->type,
                        'flagable_id' => $data->id,
                        'flagable_type' => SaleReturn::class,
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key] ,
                        'color_id' => $request->color_id[$key] ,
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'total_price' => $request->unit_price[$key] * $request->quantity[$key],
                        'created_at' => now(),
                    ];
                    ProductIn::create($storeDataIn);
                }

            }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
                $request->session()->flash('successMessage', 'Sale Return was Not Updated! ' . $e);
                return back();
            }
        $request->session()->flash('successMessage', 'Sale Return was successfully Updated!');
        return redirect()->route('admin.sale.return.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete sale-return');
        $data = SaleReturn::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Sale Return data not found!');
            return redirect()->route('admin.sale.sale_return.index', qArray());
        } 
        DB::beginTransaction();
        try{
            $sale = Sale::find($data->sale_id);
            if ($sale) {
                $updateData = [
                    'has_return' => 'No',
                    'updated_by' => auth()->user()->id
                ];
                if($sale->status == 'Canceled'){
                    $updateData['status'] = 'Processing';
                }
                $sale->update($updateData);
            }
            foreach($data->items as $item){
                if($item->used_quantity > 0){
                    $request->session()->flash('errorMessage', 'This Return item has already been consumed !!');
                    return redirect()->route('admin.sale.return.index', qArray());
                }
                
            }

            foreach($data->items as $item) {

                $productUses = ProductUse::where('product_out_id', $item->id)->get();
                foreach($productUses as $use) {
                    $use->productIn()->update(['return_quantity' => 0]);
                }
                $qty = $item->quantity;
                foreach($productUses as $use) {
                    $useQty = $use->quantity;
                    if($qty > $useQty){
                        $use->productIn->update(['return_quantity' => $use->productIn->return_quantity - $useQty]);
                        $qty = $qty - $useQty;
                    } else {
                        $use->productIn->update(['return_quantity' => $use->productIn->return_quantity - $qty]);
                        break;
                    }
                }
                $item->delete();
            }
            $data->delete();
            DB::commit();
            $request->session()->flash('successMessage', 'Sale Return was successfully deleted!');
            return redirect()->route('admin.sale.return.index', qArray());
        }catch (\Exception $e){
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Sale Return was not deleted! ' . $e);
            return redirect()->route('admin.sale.return.index', qArray());
        }
    }

    public function getSale(Request $request)
    {
        $sql = Sale::with([
            'items',
            'items.items',
            'items.product',
            'items.unit',
            'items.color',
            'createdBy',
            'updatedBy',
            'user',
            'customer',
            'resellerBusiness',
            'saleReturn',
        ]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('user_id', auth()->user()->id);
        }
        $data['sale'] = $sql->where('code', $request->code)->first();
        $data['code'] = $request->code;
        if($data['sale']){
            if($data['sale']->has_return == 'Yes'){
                $data['sale'] = null;
                $data['msg'] = 'Sale already has return';
            } else{
                $data['msg'] = 'Sale found';
            }
            
        } else {
            $data['msg'] = 'No Sale found';
        }
        return view('admin.sale.sale_return', $data)->with('create', 1);
    }
}