<?php

namespace App\Http\Controllers\Api\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiOrderRequest;
use App\Models\Bank;
use App\Models\Color;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use App\Services\CodeService;
use App\Services\OrderService;
use Barryvdh\Snappy\Facades\SnappyImage;
use Barryvdh\Snappy\ImageWrapper;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @authenticated
     * @responseFile responses/sale/orders.json
     */
    public function index()
    {
        try {
            $data = Order::select([
                'id',
                'code',
                'date',
                'note',
                'shipping_charge',
                'amount',
                'status',
                'customer_id',
            ])
                ->with([
                    'items',
                    'items.product:id,name',
                    'items.color:id,name',
                    'items.unit:id,name',
                    'images',
                    'customer:id,name as customer_name,contact_name,email,mobile,address,shipping_address',
                ])
                ->where('user_id', auth()->user()->id)
                ->get();
            return response()->json([
                'status' => true,
                'message' => 'Orders Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function create()
    {
        $data['products'] = Product::select(['products.id', 'products.name', 'product_type', 'sale_price as unit_price', DB::raw('ifnull(category_type, "Regular") as category'), 'product_fabrics.fabric_product_id', 'units.name as unit'])
            ->leftJoin('units', 'units.id', '=', 'products.unit_id')
            ->leftJoin('product_fabrics', 'products.id', '=', 'product_fabrics.product_id')
            ->whereIn('product_type', ['Base', 'Combo', 'Product'])
            ->where('products.status', 'Active')
            ->with('items.product')
            ->get();
        $data['fabrics'] = Product::where('status', 'Active')->where('product_type', 'Fabric')->get(['id', 'name']);
        $data['colors'] = Color::where('status', 'Active')->get();
        $data['banks'] = Bank::where('status', 'Active')->get(['id', 'bank_name', 'account_name', 'account_no']);
        $data['delivery_agents'] = DeliveryAgent::where('status', 'Active')->latest()->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'message' => 'Order create provided.',
            'data' => $data,
        ]);
    }

    public function store(ApiOrderRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $code = CodeService::generate(Order::class, 'SO', 'code');
            $storeData = [
                'code' => $code,
                'date' => date('Y-m-d'),
                'customer_id' => $validatedData['customer_id'],
                'note' => $validatedData['note'],
                'delivery_agent_id' => $validatedData['delivery_agent_id'],
                'shipping_charge' => $validatedData['shipping_charge'] ?? 0,
                'advance_amount' => $validatedData['advance_amount'] ?? 0,
                'discount_amount' => $validatedData['discount_amount'] ?? 0,
                'amount' => $validatedData['order_amount'],
                'status' => 'Ordered',
                'type' => auth()->user()->role,
                'user_id' => auth()->user()->id,
                'created_by' => auth()->user()->id,
            ];
            $data = Order::create($storeData);
            if ($data) {
                if ($validatedData['items']) {
                    foreach ($validatedData['items'] as $item) {
                        $product = Product::find($item['id']);
                        $createItemData = [
                            'order_id' => $data->id,
                            'product_id' => $product->id,
                            'unit_id' => $product->unit_id,
                            'color_id' => null, //$item['color_id'] == 0 ? null : $item['color_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'amount' => $item['amount'],
                        ];
                        OrderItem::create($createItemData);
                    }
                }
            }
            if ($request->input('advance_amount') > 0) {
                $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');
                $storeData = [
                    'customer_id' => $data->customer_id,
                    'order_id' => $data->id,
                    'type' => 'Received',
                    'date' => dbDateFormat($data->date),
                    'receipt_no' => $code,
                    'amount' => $data->advance_amount ?? 0,
                    'note' => $data->note,
                    'created_by' => auth()->user()->id,
                ];

                $payData = CustomerPayment::create($storeData);
                Transaction::insert([
                    'type' => 'Received',
                    'flag' => 'Customer',
                    'flagable_id' => $payData->id,
                    'flagable_type' => CustomerPayment::class,
                    'note' => $payData->note,
                    'bank_id' => $validatedData['bank_id'],
                    'datetime' => $payData->date,
                    'amount' => $payData->amount,
                    'created_by' => auth()->user()->id,
                    'created_at' => now(),
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Order Store Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @authenticated
     * @responseFile responses/sale/single-order.json
     */
    public function show($id)
    {
        try {
            $data = Order::select([
                    'orders.*',
                    'delivery_agents.name as delivery_agent_detail',
                    DB::raw("concat(customers.name, ' [', customers.mobile, ']') as customer_detail"),
                    DB::raw("concat(banks.bank_name, ' [', banks.account_no, ']') as bank_detail"),
                ])
                ->with(['items', 'images'])
                ->leftJoin('delivery_agents', 'delivery_agents.id', '=', 'delivery_agent_id')
                ->leftJoin('customers', 'customers.id', '=', 'customer_id')
                ->leftJoin('banks', 'banks.id', '=', 'bank_id')
                ->where('orders.id', $id)->first();

            return response()->json([
                'status' => true,
                'message' => 'Order Details Provided.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @authenticated
     * @responseFile responses/sale/single-order.json
     */
    public function edit($id)
    {
        $data['data'] = Order::with('items', 'items.product', 'images', 'customer', 'resellerBusiness', 'user', 'createdBy', 'updatedBy', 'delivery', 'shipping')->find($id);
        $data['items'] = $data['data']->items;
        $data['customers'] = Customer::where('status', 'Active')->where('type', $data['data']->type)->get(['id', 'name', 'mobile']);
        $data['users'] = User::where('role', $data['data']->type)->latest()->get(['id', 'name']);
        $data['delivery_agents'] = DeliveryAgent::where('status', 'Active')->latest()->get(['id', 'name']);
        $data['products'] = Product::with(['unit', 'item'])
        ->whereIn('cover_type', ['Base', 'Package'])
        ->orWhereIn('other_type', ['Product', 'Combo'])
        ->where('status', 'Active')->get();
        $data['colors'] = Color::where('status', 'Active')->get();
        $data['units'] = Unit::where('status', 'Active')->get();
        $data['banks'] = Bank::where('status', 'Active')->get(['id', 'bank_name', 'account_name', 'account_no']);
        return response()->json([
            'status' => true,
            'message' => 'Order edit provided.',
            'data' => $data,
        ]);
    }

    public function update(ApiOrderRequest $request, $id)
    {
        $validatedData = $request->validated();

        $data = Order::with(['images'])->findOrFail($id);

        try {
            $updatableData = [
                'customer_id' => $validatedData['customer_id'],
                'note' => $validatedData['note'],
                'delivery_agent_id' => $validatedData['delivery_agent_id'],
                'shipping_charge' => $validatedData['shipping_charge'] ?? 0,
                'advance_amount' => $validatedData['advance_amount'] ?? 0,
                'discount_amount' => $validatedData['discount_amount'] ?? 0,
                'amount' => $validatedData['order_amount'],
                'updated_by' => auth()->user()->id,
            ];
            $data->update($updatableData);

            if ($validatedData['items']) {
                $data->items()->delete();
                foreach ($validatedData['items'] as $item) {
                    $product = Product::find($item['id']);
                    OrderItem::create([
                        'order_id' => $data->id,
                        'product_id' => $product->id,
                        'unit_id' => $product->unit_id,
                        'color_id' => null, //$item['color_id'] == 0 ? null : $item['color_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'amount' => $item['amount'],
                    ]);
                }
            }

            if ($data->advance_amount != $validatedData['advance_amount'] || $data->bank_id != $validatedData['bank_id']) {
                $advance_amount = $validatedData['advance_amount'] ?? 0;
                $payment = CustomerPayment::where('type', 'Received')->where('order_id', $data->id)->first();
                if ($payment) {
                    $payment->transactions()->delete();
                    $payment->delete();
                }
                $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');

                $customerPayment = CustomerPayment::create([
                    'order_id' => $data->id,
                    'customer_id' => $validatedData['customer_id'],
                    'type' => 'Received',
                    'date' => now(),
                    'receipt_no' => $code,
                    'amount' => $advance_amount,
                    'note' => $validatedData['note'],
                    'created_by' => Auth::user()->id,
                ]);

                Transaction::create([
                    'type' => 'Received',
                    'flag' => 'Customer',
                    'flagable_id' => $customerPayment->id,
                    'flagable_type' => CustomerPayment::class,
                    'amount' => $advance_amount,
                    'bank_id' => $validatedData['bank_id'],
                    'note' => $customerPayment->note,
                    'datetime' => $customerPayment->date,
                    'created_by' => Auth::user()->id,
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Order Updated Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            OrderService::delete($id);
            return response()->json([
                'status' => true,
                'message' => 'Order was successfully deleted.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
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
    public function monthly(Request $request)
    {
        try {
            $month = Carbon::createFromDate($request->input('year'), $request->input('month'));
            $rangeBetween = [$month->clone()->startOfMonth(), $month->clone()->endOfMonth()];
            $query = Order::select([
                'orders.id',
                'orders.code',
                'orders.date', 
                'orders.note', 
                'orders.shipping_charge', 
                'orders.advance_amount', 
                'orders.discount_amount', 
                'orders.amount', 
                'C.paid_amount',
                DB::raw('CASE 
                    WHEN C.paid_amount IS NULL THEN "No"
                    WHEN C.paid_amount = orders.amount THEN "Full"
                    ELSE "Partial"
                    END AS payment_status'
                ),
                'orders.status', 
                'orders.created_at', 
                'orders.updated_at', 
                'customers.name as customer_name' , 
                'delivery_agents.name as delivery_agent_name'
            ])->with([
                'items' => function($q) {
                    $q->addSelect([
                        'order_items.id', 
                        'order_items.order_id', 
                        'products.name as product_name' , 
                        'colors.name as color_name',
                        'units.name as unit_name',
                        'products.type as product_type' , 
                        'products.category as product_category' , 
                        'order_items.quantity', 
                        'order_items.unit_price', 
                        'order_items.amount', 
                    ]);
                    $q->leftJoin('products', 'products.id', '=', 'order_items.product_id');
                    $q->leftJoin('colors', 'colors.id', '=', 'order_items.color_id');
                    $q->leftJoin('units', 'units.id', '=', 'order_items.unit_id');
                }
            ])
            ->leftJoin('customers', 'orders.customer_id','=','customers.id')
            ->leftJoin('delivery_agents', 'orders.delivery_agent_id','=','delivery_agents.id')
            ->leftJoin(DB::raw("(SELECT 
                customer_payments.order_id, 
                SUM(customer_payments.amount) AS paid_amount 
                FROM `customer_payments` WHERE customer_payments.type='Received' 
                GROUP BY customer_payments.order_id) AS C
            "), function($q) {
                $q->on('C.order_id', '=', 'orders.id');
            })
            ->whereBetween('orders.date', $rangeBetween);
            if (!(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))) {
                $query->where('orders.user_id', auth()->user()->id);
            }
            $data = $query->get();
            return response()->json([
                'status' => true,
                'message' => 'Monthly Orders Request Successfully',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function myLedger(Request $request)
    {
        try {
            $user = auth()->user()->id;
            $dateCond = '';
            $from = '1970-01-01';
            $to = date('Y-m-d');
            if ($request->from) {
                $from = $request->from;
                $dateCond .= "AND DATE(X.date) >= '".dbDateFormat($from)."'";
            }
            if ($request->to) {
                $to = $request->to;
                $dateCond .= "AND DATE(X.date) <= '".dbDateFormat($to)."'";
            }
            // Report Lists
            $query1 = "SELECT `code`,`date`,'Orders' AS type,note, 'admin.orders.show' AS route, id, 
            IFNULL(amount, 0) AS amount FROM orders AS X WHERE user_id = $user $dateCond";
            $query2 = "SELECT `code`,`date`,'Sales' AS type,note, 'admin.sale.sales.show' AS route, id, IFNULL(total_amount, 0) AS amount FROM sales AS X WHERE user_id = $user $dateCond";
            $query3 = "SELECT `code`,`date`,'Sale Returns' AS type,note, 'admin.sale.return.show' AS route, id, IFNULL(return_amount, 0) AS amount FROM sale_returns AS X WHERE user_id = $user $dateCond";
            $query4 = "SELECT `X`.`receipt_no` AS code,`X`.`date` AS date,'Received' AS type,`X`.`note` AS note,'admin.payment.customer-payments.show' AS route, `X`.`id`, `X`.`amount` AS amount FROM `customer_payments` AS X JOIN `orders` ON `X`.`order_id` = `orders`.`id` WHERE `X`.`type` = 'Received' AND `orders`.`user_id` = $user $dateCond";
            $query5 = "SELECT `receipt_no` AS code,`date`,'Payment' AS type,note, 'admin.payment.reseller-payments.show' AS route, id, total_amount AS amount FROM reseller_payments AS X WHERE type='Payment' AND reseller_id = $user $dateCond";

            $reports = DB::select("SELECT S.* FROM ($query1 UNION ALL $query2 UNION ALL $query3 UNION ALL $query4 UNION ALL $query5) S ORDER BY S.`date` ASC");
            return response()->json([
                'status' => true,
                'data' => $reports,
                'message' => 'My Ledger Request Successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
