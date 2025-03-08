<?php

namespace App\Http\Controllers\Admin\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Bank;
use App\Models\Color;
use App\Models\Customer;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Models\Unit;
use App\Models\User;
use App\Services\OrderService;
use Barryvdh\Snappy\Facades\SnappyImage;
use Barryvdh\Snappy\ImageWrapper;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('list orders');
        $sql = Order::orderBy('id', 'DESC')->with([
            'items',
            'items.product',
            'items.unit',
            'items.color',
            'images',
            'customer',
            'resellerBusiness',
            'user',
            'createdBy',
            'updatedBy',
            'delivery',
            'shipping'
        ]);
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('user_id', auth()->user()->id);
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
        if ($request->type) {
            $sql->where('type', $request->type);
        }
        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->q . '%');
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

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        $customer = Customer::select('id', 'name', 'mobile')->where('status', 'Active');
        if ((!auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $customer->where('user_id', auth()->user()->id);
        }
        $customers = $customer->get();

        return view('admin.sale.order', compact('result', 'customers'))->with('list', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add orders');
        $data['items'] = [
            (object)[
                'id' => null,
                'product_id' => null,
                'product' => null,
                'unit_id' => null,
                'color_id' => null,
                'quantity' => null,
                'unit_price' => null,
                'amount' => null
            ]
        ];
        $customer = Customer::select('id', 'name', 'mobile')->where('status', 'Active');
        if ((!auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $customer->where('user_id', auth()->user()->id);
        }
        $data['customers'] = $customer->get();
        $data['products'] = Product::with(['unit', 'item'])->whereIn('product_type', ['Base', 'Base-Ready-Production', 'Product', 'Combo'])->where('status', 'Active')->get();
        $data['colors'] = Color::where('status', 'Active')->get();
        $data['units'] = Unit::where('status', 'Active')->get();
        $data['banks'] = Bank::where('status', 'Active')->get();
        $data['delivery_agents'] = DeliveryAgent::where('status', 'Active')->latest()->get(['id', 'name']);

        return view('admin.sale.order', $data)->with('create', 1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        $this->authorize('add orders');
        DB::beginTransaction();
        try {
            OrderService::store($request);
            DB::commit();
            $request->session()->flash('successMessage', 'Order was successfully added!');
            return redirect()->route('admin.orders.create', qArray());
        } catch (Exception $e) {
            DB::rollBack();
            $request->session()->flash('errorMessage', $e->getMessage());
            return redirect()->route('admin.orders.create', qArray());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('show orders');
        $data['data'] = Order::with(
            'items', 
            'items.product', 
            'images', 'customer', 
            'resellerBusiness', 
            'user', 'createdBy', 'updatedBy', 
            'delivery', 'shipping'
            )->findOrFail($id);

        return view('admin.sale.order', $data)->with('show', $id);
    }

    public function edit($id)
    {
        $this->authorize('edit orders');

        $data['data'] = Order::with(
            'items', 'items.product', 
            'images', 'customer', 'resellerBusiness', 
            'user', 'createdBy', 'updatedBy', 
            'delivery', 'shipping'
            )->select('orders.*', 'transactions.bank_id', 'customer_payments.amount AS paid_amount')
            ->leftJoin('customer_payments', function ($q) {
                $q->on('customer_payments.order_id', '=', 'orders.id');
            })
            ->leftJoin('transactions', function ($q) {
                $q->on('transactions.flagable_id', '=', 'customer_payments.id');
                $q->where('flag' , 'Customer');
            })
            ->findOrFail($id);
        $data['items'] = $data['data']->items;
        $customer = Customer::select('id', 'name', 'mobile')->where('status', 'Active');
        if ((!auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $customer->where('user_id', auth()->user()->id);
        }
        $data['customers'] = $customer->get();
        $data['users'] = User::where('role', $data['data']->type)->latest()->get(['id', 'name']);
        $data['delivery_agents'] = DeliveryAgent::where('status', 'Active')->latest()->get(['id', 'name']);
        $data['products'] = Product::with(['unit', 'item'])->whereIn('product_type', ['Base', 'Product','Combo'])->where('status', 'Active')->get();
        $data['colors'] = Color::where('status', 'Active')->get();
        $data['units'] = Unit::where('status', 'Active')->get();
        $data['banks'] = Bank::where('status', 'Active')->get();

        return view('admin.sale.order', $data)->with('edit', $id);
    }

    public function update(OrderRequest $request, $id)
    {
        $this->authorize('edit orders');
        DB::beginTransaction();
        try {
            OrderService::update($request, $id);
            DB::commit();
            $request->session()->flash('successMessage', 'Order was successfully updated!');
            return redirect()->route('admin.orders.index');

        } catch (Exception $e) {
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Error Occurred!! ' . $e);
            return redirect()->route('admin.orders.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete orders');
        try {
            OrderService::delete($id);
            $request->session()->flash('successMessage', 'Order was successfully deleted!');
            return redirect()->route('admin.orders.index', qArray());
        } catch (Exception $e) {
            $request->session()->flash('errorMessage', 'Error Occured!! ' . $e);
            return redirect()->route('admin.orders.index', qArray());
        }
    }

    public function orderPrint(Request $request)
    {
        $this->authorize('print orders');

        $sql = Order::with([
            'items',
            'items.product',
            'items.unit',
            'items.color',
            'images',
            'customer',
            'resellerBusiness',
            'user',
            'createdBy',
            'updatedBy'
        ]);
        if (!auth()->user()->hasRole('Super Admin')) {
            $sql->where('created_by', auth()->user()->id);
        }

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('amount', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('status', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('date', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('customer', function ($q) use ($request) {
                $q->where('name', $request->q);
                $q->orWhere('mobile', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('items.product', function ($q) use ($request) {
                $q->where('name', $request->q);
            });
            $sql->orwhereHas('resellerBusiness', function ($q) use ($request) {
                $q->where('name', $request->q);
            });
            $sql->orwhereHas('user', function ($q) use ($request) {
                $q->where('name', $request->q);
            });
            $sql->orwhereHas('createdBy', function ($q) use ($request) {
                $q->where('name', $request->q);
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
        if ($request->type) {
            $sql->where('type', $request->type);
        }

        $result = $sql->latest('id')->paginate($request->limit ?? config('settings.per_page_limit'));

        if ($request['action'] == 'print') {
            return view('admin.sale.order-print-page', compact('result'));
        }
        $customer = Customer::select('id', 'name', 'mobile')->where('status', 'Active');
        if ((!auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $customer->where('user_id', auth()->user()->id);
        }
        $customers = $customer->get();
        return view('admin.sale.order-print', compact('customers', 'result'));
    }

    public function snap($id)
    {
        $data['data'] = Order::with([
            'items',
            'items.product',
            'items.unit',
            'items.color',
            'images',
            'customer',
            'resellerBusiness',
            'user',
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);
        /**
         * @var ImageWrapper $snappy
         */
        $snappy = SnappyImage::loadView('snaps.order', $data);
        return $snappy->inline('order.jpg');
    }

    public function getOrder(Request $request)
    {
        $order = Order::with([
            'items' => function (HasMany $q) {
                $q->whereHas('productFabric.fabric', function ($q) {
                    $q->where('name', 'LIKE', '%' . 'Turkey' . '%');
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
            ->whereIn('has_stock_done', ['No', 'Yes'])
            ->first();
        if ($order) {
            return response()->json(['success' => true, 'data' => $order]);
        } else {
            return response()->json(['success' => false, 'data' => 'No data found!']);
        }
    }

    public function paymentOrder(Request $request)
    {
        $order = Order::with('customer', 'resellerBusiness')->where('code', $request->code)->where('status', 'Processing')->first();
        $total_amount = ($order->amount);
        $customerPayment = $order->customerPayment;
        $order_due = ($total_amount - $customerPayment);
        $order_info = [
            'order_id' => $order->id,
            'customer' => $order->customer,
            'total_amount' => $total_amount,
            'customer_pay' => $customerPayment,
            'order_due' => $order_due,
            'delivery_agent_id' => $order->delivery_agent_id,
            'delivery_charge' => $order->shipping_charge,
        ];
        if ($order) {
            return response()->json(['success' => true, 'data' => $order_info]);
        } else {
            return response()->json(['success' => false, 'data' => 'No data found!']);
        }
    }

    public function pendingSale(Request $request)
    {
        $this->authorize('list orders');
        $fourDaysAgo = Carbon::now()->subDays(4);

        $sql = Order::orderBy('id', 'DESC')->with([
            'items',
            'items.product',
            'items.unit',
            'items.color',
            'images',
            'customer',
            'resellerBusiness',
            'user',
            'createdBy',
            'updatedBy',
            'delivery',
            // 'shipping'
        ])
            ->whereDate('created_at', '<=', $fourDaysAgo)->where('status', 'Ordered');
        if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $sql->where('user_id', auth()->user()->id);
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
        if ($request->type) {
            $sql->where('type', $request->type);
        }
        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('code', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('amount', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('status', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('date', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('customer', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('mobile', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('items.product', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('resellerBusiness', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('mobile', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('user', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('createdBy', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        $result = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        $customer = Customer::select('id', 'name', 'mobile')->where('status', 'Active');
        if ((!auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
            $customer->where('user_id', auth()->user()->id);
        }
        $customers = $customer->get();

        $shipping_methods = ShippingRate::where('status', 'Active')->latest()->get(['id', 'name', 'rate']);

        return view('admin.sale.pending-sale', compact('result', 'customers', 'shipping_methods'))->with('list', 1);
    }
}
