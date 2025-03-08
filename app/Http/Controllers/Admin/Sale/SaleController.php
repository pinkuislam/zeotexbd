<?php

namespace App\Http\Controllers\Admin\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\ProductUse;
use App\Models\CustomerPayment;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\Sale;
use App\Models\ProductIn;
use App\Models\ResellerBusinessPayment;
use App\Models\SaleReturn;
use App\Models\Transaction;
use App\Services\CodeService;
use App\Services\CustomerService;
use App\Services\ResellerBusinessService;
use App\Services\SaleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SaleController extends Controller
{
    protected $type = "Sale";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list sale');
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
            'delivery',
            'shipping'
        ]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('created_by',auth()->user()->id);
        }
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%');
            });
            
            $sql->orwhereHas('customer', function ($q) use ($request) {
                $q->where('name', $request->q);
                $q->orWhere('mobile', 'LIKE', '%' . $request->q . '%');
            });
            
            $sql->orwhereHas('resellerBusiness', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('mobile', 'LIKE', '%' . $request->q . '%');
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
        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $result = $sql->latest()->paginate($request->limit ?? config('settings.per_page_limit'));

        $customer = Customer::select('id','name','mobile')->where('status','Active');
        if ((!auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))){
            $customer->where('user_id',auth()->user()->id);
        }
        $customers = $customer->get();
        
        return view('admin.sale.sales', compact('result', 'customers'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add sale');
        $data['code'] = ''; 
        return view('admin.sale.sales', $data)->with('create', 1);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaleRequest $request)
    {
        $this->authorize('add sale');
        DB::beginTransaction();
        try {
            $data = SaleService::store($request);
            DB::commit();
            if ($data) {
                return redirect()->route('admin.sale.sales.invoice', $data->id);
            }
            return back();
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
        $this->authorize('show sale');
        $data['data'] = Sale::with('items','items.product','items.unit','items.color','createdBy','updatedBy','user','customer','resellerBusiness','delivery','shipping')->find($id);
        $data['banks'] = Bank::where('status','Active')->latest()->get();
        $data['delivery_agents'] = DeliveryAgent::where('status','Active')->latest()->get(['id','name']);
        if ($data['data']->customer_id) {
            $data['due'] = CustomerService::due($data['data']->customer_id);
        } else {
            $data['due'] = ResellerBusinessService::due($data['data']->reseller_business_id);
        }
        return view('admin.sale.sales', $data)->with('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('edit sale');
        $data['data'] = Sale::with([
            'items',
            'items.product',
            'items.unit',
            'items.color',
            'createdBy',
            'updatedBy',
            'user',
            'customer',
            'resellerBusiness',
            'delivery',
            'shipping'
        ])->find($id);
        $sql = Order::with('items')->where('code',$data['data']->order->code);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('user_id',auth()->user()->id);
        }
        $data['order'] = $sql->first();
        $data['code'] = $data['data']->order->code;
        // $data['shipping_methods'] = ShippingRate::where('status','Active')->latest()->get(['id','name','rate']);
        $data['delivery_agents'] = DeliveryAgent::where('status','Active')->latest()->get(['id','name']);
        return view('admin.sale.sales', $data)->with('edit', $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaleRequest $request,$id)
    {
        $this->authorize('edit sale');
        DB::beginTransaction();
        try {
            SaleService::update($request, $id);
            DB::commit();
            $request->session()->flash('successMessage', 'Sale was successfully Updated!');
            return redirect()->route('admin.sale.sales.index');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            $request->session()->flash('errorMessage', 'Sale was Not Updated! ' . $e);
            return back();
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
        $this->authorize('delete sale');
        DB::beginTransaction();
        try{
           SaleService::delete($id);
            DB::commit();
            $request->session()->flash('successMessage', 'Sale was successfully deleted!');
            return redirect()->route('admin.sale.sales.index', qArray());
        }catch (\Exception $e){
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Sale was not deleted! ' . $e);
            return redirect()->route('admin.sale.sales.index', qArray());
        }
    }

    public function getOrder(Request $request)
    {
        
        $sql = Order::with([
            'items',
            'items.product',
            'items.product.items',
            'customer',
            'resellerBusiness',
            'user',
            'createdBy',
            'updatedBy'
            ])
            ->where('code', $request->code)
            ->where('status', 'Ordered');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('user_id', auth()->user()->id);
        }
        $order = $sql->first();
        if($order){
            $orderStock = 'Yes';
            foreach($order->items as $item){
                if($item->product->master_type == 'Cover'){
                    if(!($item->product->product_type == 'Base-Ready-Production')){
                        if($order->has_stock_done == 'No'){
                            $orderStock = 'No';
                            break;
                        }
                    }
                    
                }
            }
            if($orderStock == 'Yes'){
                $data['order'] = $order;
            } else {
                $data['order'] = null;
            }
        } else {
            $data['order'] = null;
        }
        
        $data['code'] = $request->code;
        $data['delivery_agents'] = DeliveryAgent::where('status','Active')->latest()->get(['id','name']);
        return view('admin.sale.sales', $data)->with('create', 1);
    }
    public function invoice($id)
    {
        $this->authorize('invoice sale');

        $data = Sale::with([
            'order',
            'items',
            'customer',
            'resellerBusiness',
            'delivery',
            'user'
        ])->find($id);
        return view('admin.sale.invoice', compact('data'));
    }
    public function updataSale(Request $request)
    {
        $this->authorize('edit sale');

        $this->validate($request, [
            'payment_amount' => 'required|numeric',
            'adjustment_amount' => 'nullable|numeric',
            'bank' => 'required',
        ]);

        $data = Sale::find($request->id);
        $updateData = [
            'status' => 'Delivered',
            'delivery_agent_id' => $request->delivery_agent_id,
            'extra_shipping_charge' => $request->extra_shipping_charge,
            'updated_by' => auth()->user()->id
        ];
        $data->update($updateData);
        if ($data->order) {
            $order = Order::find($data->order->id);
            $updateorderData = [
                'status' => 'Delivered',
                'updated_by' => auth()->user()->id
            ];
            $order->update($updateorderData);
        }
        if ($request->payment_amount > 0) {
            if ($data->customer_id) {
                $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');
                $storeData = [
                    'customer_id' => $data->customer_id,
                    'sale_id' => $data->id,
                    'type' => 'Received',
                    'is_advance' => 'Yes',
                    'date' => dbDateFormat($data->date),
                    'receipt_no' => $code,
                    'amount' => $request->payment_amount,
                    'note' => $data->note,
                    'created_by' => auth()->user()->id,
                ];
        
                $payment = CustomerPayment::create($storeData);
        
                if ($payment && $payment->type != 'Adjustment') {
                    $transactionData[] = [
                        'type' => $payment->type,
                        'flag' => 'Customer',
                        'flagable_id' => $payment->id,
                        'flagable_type' => CustomerPayment::class,
                        'note' => $payment->note,
                        'bank_id' => $request->bank,
                        'datetime' => $payment->date,
                        'amount' => $payment->amount,
                        'created_by' => auth()->user()->id,
                        'created_at' => now(),
                    ];
                    Transaction::insert($transactionData);
                }
            }
    
            if ($data->reseller_business_id) {
                $code = CodeService::generate(ResellerBusinessPayment::class, '', 'receipt_no');
    
                $storeData = [
                    'reseller_business_id' => $data->reseller_business_id,
                    'sale_id' => $data->id,
                    'type' => 'Received',
                    'date' => dbDateFormat($data->date),
                    'receipt_no' => $code,
                    'amount' => $request->payment_amount,
                    'note' => $data->note,
                    'created_by' => auth()->user()->id,
                ];
        
                $payment = ResellerBusinessPayment::create($storeData);
        
                if ($data && $data->type != 'Adjustment') {
                    if ($request->only('transaction_id')) {
                        $transactionData = [];
                        foreach ($request->transaction_id as $key => $tinId) {
                            $transactionData[] = [
                                'type' => $payment->type,
                                'flag' => 'Reseller Business',
                                'flagable_id' => $payment->id,
                                'flagable_type' => ResellerBusinessPayment::class,
                                'note' => $payment->note,
                                'bank_id' => $request->bank,
                                'datetime' => $payment->date,
                                'amount' => $payment->amount,
                                'created_by' => auth()->user()->id,
                                'created_at' => now(),
                            ];
                        }
                        Transaction::insert($transactionData);
                    }
                }
            }
        }
        if ($request->adjustment_amount > 0) {
            if ($data->customer_id) {
                $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');
                $storeData = [
                    'customer_id' => $data->customer_id,
                    'sale_id' => $data->id,
                    'type' => 'Adjustment',
                    'date' => dbDateFormat($data->date),
                    'receipt_no' => $code,
                    'amount' => $request->adjustment_amount,
                    'note' => $data->note,
                    'created_by' => auth()->user()->id,
                ];
                $payment = CustomerPayment::create($storeData);
            }
            if ($data->reseller_business_id) {
                $code = CodeService::generate(ResellerBusinessPayment::class, '', 'receipt_no');
                $storeData = [
                    'reseller_business_id' => $data->reseller_business_id,
                    'sale_id' => $data->id,
                    'type' => 'Received',
                    'date' => dbDateFormat($data->date),
                    'receipt_no' => $code,
                    'amount' => $request->adjustment_amount,
                    'note' => $data->note,
                    'created_by' => auth()->user()->id,
                ];
                $payment = ResellerBusinessPayment::create($storeData);
            }
        }
        return response()->json(['success' => true, 'successMessage' => 'Sale Delivery was successfully updated!']);
      
    }
    public function saleCanceled($id)
    {
        $this->authorize('edit sale');
        DB::beginTransaction();
        try {
            $data = Sale::find($id);
            if ($data->status != 'Canceled') {
                $updateData = [
                    'status' => 'Canceled',
                    'has_return' => 'Yes',
                    'updated_by' => auth()->user()->id
                ];
                $data->update($updateData);
                if ($data->order) {
                    $order = Order::find($data->order_id);
                    $updateorderData = [
                        'status' => 'Canceled',
                        'updated_by' => auth()->user()->id
                    ];
                    $order->update($updateorderData);
                }
                $storeData = [
                    'code' => $data->code,
                    'date' => dbDateFormat($data->date),
                    'user_id' => $data->user_id ?? auth()->user()->id,
                    'sale_id' => $data->id,
                    'customer_id' => $data->customer_id,
                    'reseller_business_id' => $data->reseller_business_id,
                    'return_amount' => $data->total_amount,
                    'note' => $data->note,
                    'cost' => $data->cost ?? 0,
                    'deduction_amount' => $data->deduction_amount,
                    'reseller_amount' => $data->reseller_amount,
                    'created_by' => auth()->user()->id
                ];
                $returnData = SaleReturn::create($storeData);
                foreach ($data->items as $key => $row) {
                    foreach($row->items as $item) {
                        $productUses = ProductUse::with('productIn')->where('product_out_id', $item->id)->get();
                        $qty = $item->quantity;
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
                            'type' => 'SaleReturn',
                            'flagable_id' => $returnData->id,
                            'flagable_type' => SaleReturn::class,
                            'product_id' => $item->product_id,
                            'unit_id' => $item->unit_id ,
                            'color_id' => $item->color_id ,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->unit_price * $item->quantity,
                            'created_at' => now(),
                        ];
                        ProductIn::create($storeDataIn);
                    }
                    
                }
                DB::commit();
                session()->flash('successMessage', 'Sale was successfully Canceled!');
                return redirect()->route('admin.sale.sales.index', qArray());
            }else {
                session()->flash('errorMessage', 'Sale already Canceled!');
                return redirect()->route('admin.sale.sales.index', qArray());
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            session()->flash('errorMessage', 'Sale Canceled error found!');
            return redirect()->route('admin.sale.sales.index', qArray());
        }
      
    }
    public function pendingDelivery(Request $request)
    {
        $fourDaysAgo = Carbon::now()->subDays(4);
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
            'delivery',
            'shipping'
        ])
        ->whereDate('created_at', '<=', $fourDaysAgo)
        ->where('status','Processing');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('created_by',auth()->user()->id);
        }
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('total_amount', 'LIKE', '%'. $request->q.'%')
                ->orWhere('status', 'LIKE', '%'. $request->q.'%')
                ->orWhere('date', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('customer', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
                $q->orWhere('mobile', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('items.product', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('resellerBusiness', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
                $q->orWhere('mobile', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('user', function($q) use($request) {
                $q->where('name', $request->q);
            });
            $sql->orwhereHas('createdBy', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
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

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        $customer = Customer::select('id','name','mobile')->where('status','Active');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $customer->where('type', auth()->user()->role);
        }
        $customers = $customer->get();
        
        return view('admin.sale.pending-delivery', compact('result', 'customers'))->with('list', 1);
    }
    public function deliveredSale(Request $request)
    {
        $fourDaysAgo = Carbon::now()->subDays(4);

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
            'delivery',
            'shipping'
        ])
        ->where('status','Delivered');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('created_by',auth()->user()->id);
        }
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('code', 'LIKE', '%'. $request->q.'%')
                ->orWhere('total_amount', 'LIKE', '%'. $request->q.'%')
                ->orWhere('status', 'LIKE', '%'. $request->q.'%')
                ->orWhere('date', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('customer', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
                $q->orWhere('mobile', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('items.product', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('resellerBusiness', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
                $q->orWhere('mobile', 'LIKE', '%'. $request->q.'%');
            });
            $sql->orwhereHas('user', function($q) use($request) {
                $q->where('name', $request->q);
            });
            $sql->orwhereHas('createdBy', function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%');
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

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        $customer = Customer::select('id','name','mobile')->where('status','Active');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $customer->where('type', auth()->user()->role);
        }
        $customers = $customer->get();
        
        return view('admin.sale.sale-delivered', compact('result', 'customers'))->with('list', 1);
    }
}
