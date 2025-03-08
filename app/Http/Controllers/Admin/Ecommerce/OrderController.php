<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DeliveryAgent;
use App\Models\EcommerceOrder;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Services\CodeService;
use App\Services\CustomerService;
use App\Services\SMSService;
use DB;
use Exception;
use Illuminate\Http\Request;
use MediaUploader;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list ecommerce-orders');

        $sql = EcommerceOrder::with('ecommerceOrders','ecommerceOrders.product');
        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('serial_number', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('name', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('phone', 'LIKE', '%'. $request->q . '%');
                $q->orWhere('total_amount', 'LIKE', '%'. $request->q . '%');
            });
        }
        if ($request->from) {
            $sql->where('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $sql->where('created_at', '<=', $request->to);
        }
        if ($request->status) {
            $sql->where('status', $request->status);
        }
        $records = $sql->orderBy('id', 'DESC')->paginate($request->limit ?? config('settings.per_page_limit'));
        return view('admin.ecommerce.order.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'required|string|max:15',
            'customer_address' => 'required|string|max:255',
            'delivery_agent_id' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
            'product_id' => 'required|array',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array',
            'unit_id.*' => 'required|integer',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric',
            'color_id' => 'nullable|array',
            'color_id.*' => 'nullable|integer',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric',
            'shipping_charge' => 'nullable|numeric',
            'advance_amount' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
        ]);
        if ($request->advance_amount > 0) {
            $request->validate([
                'bank_id' => 'required|integer',
            ]);
        }
        DB::beginTransaction();
        try {
            $customer = Customer::where('mobile',$request->customer_mobile)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'mobile' => $request->customer_mobile,
                    'address' => $request->customer_address,
                    'opening_due' => 0,
                    'type' => 'Admin',
                    'status' => 'Active',
                    'created_by' => auth()->user()->id,
                ]);
            }
            $storeData = [
                'code' => $request->code,
                'customer_id' => $customer->id,
                'date' => dbDateFormat($request->date) ?? date('Y-m-d'),
                'note' => $request->note,
                'delivery_agent_id' => $request->delivery_agent_id,
                'shipping_charge' => $request->shipping_charge ?? 0,
                'advance_amount' => $request->advance_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'amount' => $request->total_amount ?? 0,
                'status' => 'Ordered',
                'type' => auth()->user()->role,
                'user_id' => auth()->user()->id,
                'created_by' => auth()->user()->id,
            ];
            $data = Order::create($storeData);
            if ($data && $request->image) {
                foreach ($request->image as $key => $img) {
                    $file = MediaUploader::imageUpload($request->image[$key], 'orders', 1, null, [600, 600], [80, 80]);
                    OrderImage::create([
                        'order_id' => $data->id,
                        'image' => $file['name'],
                    ]);
                }
            }
    
            if ($data && count($request->product_id) > 0) {
                foreach ($request->product_id as $key => $product) {
    
                    $createItemData = [
                        'order_id' => $data->id,
                        'product_id' => $product,
                        'unit_id' => $request->unit_id[$key],
                        'color_id' => $request->color_id[$key],
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key] ?? 0,
                        'amount' => $request->amount[$key] ?? 0,
                    ];
                    OrderItem::create($createItemData);
                }
            }
            if ($request->advance_amount > 0) {
                $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');
                $storeData = [
                    'customer_id' => $data->customer_id,
                    'order_id' => $data->id,
                    'type' => 'Received',
                    'date' => dbDateFormat($request->date),
                    'receipt_no' => $code,
                    'amount' => $request->advance_amount ?? 0,
                    'note' => $request->note,
                    'created_by' => auth()->user()->id,
                ];
    
                $data = CustomerPayment::create($storeData);
                Transaction::insert([
                    'type' => 'Received',
                    'flag' => 'Customer',
                    'flagable_id' => $data->id,
                    'flagable_type' => CustomerPayment::class,
                    'note' => $data->note,
                    'bank_id' => $request->bank_id,
                    'datetime' => $data->date,
                    'amount' => $request->advance_amount ?? 0,
                    'created_by' => auth()->user()->id,
                    'created_at' => now(),
                ]);
            }
            $order = EcommerceOrder::where('serial_number', $request->code)->first();
            $order->update([
                'status' => 'Processing'
            ]);
            $sms = 'Your order has confirmed. Order Tracking number '. $request->code. '. Thank you, https://zeotexbd.com';
          SMSService::sendSMS('Customer', $request->customer_name, $sms, $request->customer_mobile);
            DB::commit();
            $request->session()->flash('successMessage', 'Order was successfully added!');
            return redirect()->route('admin.ecommerce.orders.index', qArray());
        } catch (Exception $e) {
            DB::rollBack();
            $request->session()->flash('errorMessage', $e->getMessage());
            return redirect()->route('admin.ecommerce.orders.index', qArray());
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
        $data = EcommerceOrder::with('ecommerceOrders','ecommerceOrders.product','shipping','ecommerceOrderImages')
        ->findOrFail($id);
        return view('admin.ecommerce.order.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = EcommerceOrder::with([
            'ecommerceOrders',
            'ecommerceOrders.product',
            'ecommerceOrders.color',
            'ecommerceOrders.size',
        ])
        ->findOrFail($id);
        $customer = Customer::where('mobile',$data->phone)->first();
        if ($customer) {
            $due = CustomerService::due($customer->id);
            $orders = Order::select('id','code','date')->where('customer_id', $customer->id)->get();
        }else {
            $orders = '';
            $due = '';
        }
        // dd($data->ecommerceOrders);
        $banks = Bank::where('status','Active')->get();
        $delivery_agents = DeliveryAgent::select('id','name')->where('status','Active')->latest()->get();
        return view('admin.ecommerce.order.edit', compact('data','delivery_agents','customer','orders','due','banks'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function status(Request $request, $id, $status)
    {

        if (!in_array($status, ['Processing', 'Shipped', 'Canceled', 'Delivered'])) {
            $request->session()->flash('eooerMessage', 'You are in wrong place.');
            return redirect()->back();
        }

        $sql = EcommerceOrder::where('id', $id);
        if ($status == 'Processing') {
            $sql->where('status', 'Placed');
        } elseif ($status == 'Canceled') {
            $sql->whereIn('status', ['Placed', 'Processing']);
        } elseif ($status == 'Shipped') {
            $sql->whereIn('status', ['Placed', 'Processing']);
        } elseif ($status == 'Delivered') {
            $sql->where('status', 'Shipped');
        }

        try {
            $data = $sql->firstOrFail();

            // OrderTrack::create([
            //     'order_id' => $data->id,
            //     'status' => $status,
            // ]);

            $updateData = ['status' => $status];
            $data->update($updateData);
    
            $request->session()->flash('successMessage', 'Order status was successfully changed to :' . $status);
        } catch (\Exception $e) {
            $request->session()->flash('eooerMessage', 'Order updating failed. reason :' . $e->getMessage());
        }

        return redirect()->back();
    }
}
