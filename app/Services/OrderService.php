<?php

namespace App\Services;

use App\Models\CustomerPayment;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Sudip\MediaUploader\Facades\MediaUploader;

class OrderService
{
    public static function store($request)
    {
        $code = CodeService::generateOrderCode(Order::class, 'SO', 'code');
        $storeData = [
            'code' => $code,
            'customer_id' => $request->customer_id,
            'reseller_business_id' => $request->reseller_business_id,
            'date' => dbDateFormat($request->date) ?? date('Y-m-d'),
            'note' => $request->note,
            'delivery_agent_id' => $request->delivery_agent_id,
            'shipping_charge' => $request->shipping_charge ?? 0,
            'advance_amount' => $request->advance_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'amount' => $request->total_amount ?? 0,
            'status' => 'Ordered',
            'created_by' => auth()->user()->id,
        ];
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            if ($request->type == "Admin" || $request->type == "Reseller Business") {
                $storeData['user_id'] = auth()->user()->id;
            } else {
                $storeData['user_id'] = $request->user_id;
            }
            $storeData['type'] = $request->type;
        } else {
            $storeData['type'] = auth()->user()->role;
            $storeData['user_id'] = auth()->user()->id;
        }
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
                    'color_id' => null,//$request->color_id[$key],
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
                'customer_id' => $request->customer_id,
                'order_id' => $data->id,
                'type' => 'Received',
                'is_advance' => 'Yes',
                'date' => dbDateFormat($request->date),
                'receipt_no' => $code,
                'amount' => $request->advance_amount ?? 0,
                'note' => $request->note,
                'created_by' => Auth::user()->id,
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
                'created_by' => Auth::user()->id,
                'created_at' => now(),
            ]);
        }

        return $data;
    }

    public static function update($request, $id)
    {
        $data = Order::with(['images'])->findOrFail($id);
        $storeData = [
            'customer_id' => $request->customer_id,
            'reseller_business_id' => $request->reseller_business_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'delivery_agent_id' => $request->delivery_agent_id,
            'shipping_charge' => $request->shipping_charge ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'advance_amount' => $request->advance_amount ?? 0,
            'amount' => $request->total_amount ?? 0,
            'status' => 'Ordered',
            'created_by' => auth()->user()->id,
        ];
        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
            if ($request->type == "Admin" || $request->type == "Reseller Business") {
                $storeData['user_id'] = auth()->user()->id;
            } else {
                $storeData['user_id'] = $request->user_id;
            }
            $storeData['type'] = $request->type;
        } else {
            $storeData['type'] = auth()->user()->role;
            $storeData['user_id'] = auth()->user()->id;
        }

        if ($request->image) {
            foreach ($data->images as $img) {
                if ($img->image) {
                    MediaUploader::delete('orders', $img->image, true);
                }
                $img->delete();
            }
            foreach ($request->image as $key => $img) {
                $file = MediaUploader::imageUpload($request->image[$key], 'orders', 1, null, [600, 600], [80, 80]);
                OrderImage::create([
                    'order_id' => $data->id,
                    'image' => $file['name'],
                ]);
            }
        }
        $data->items()->delete();

        foreach ($request->product_id as $key => $product) {
            $createItemData = [
                'order_id' => $data->id,
                'product_id' => $product,
                'unit_id' => $request->unit_id[$key],
                'color_id' => null,//$request->color_id[$key],
                'quantity' => $request->quantity[$key],
                'unit_price' => $request->unit_price[$key] ?? 0,
                'amount' => $request->amount[$key] ?? 0,
            ];
            OrderItem::create($createItemData);
        }

        if ($data->advance_amount != $request->advance_amount || $data->bank_id != $request->bank_id) {
            $advance_amount = $request->advance_amount ?? 0;
            $payment = CustomerPayment::where('type', 'Received')->where('order_id', $data->id)->first();
            if ($payment) {
                $payment->transactions()->delete();
                $payment->delete();
            }
            $code = CodeService::generate(CustomerPayment::class, '', 'receipt_no');

            $customerPayment = CustomerPayment::create([
                'order_id' => $data->id,
                'customer_id' => $request->customer_id,
                'type' => 'Received',
                'is_advance' => 'Yes',
                'date' => dbDateFormat($request->date),
                'receipt_no' => $code,
                'amount' => $advance_amount,
                'note' => $request->note,
                'created_by' => Auth::user()->id,
            ]);

            Transaction::create([
                'type' => 'Received',
                'flag' => 'Customer',
                'flagable_id' => $customerPayment->id,
                'flagable_type' => CustomerPayment::class,
                'amount' => $advance_amount,
                'bank_id' => $request->bank_id,
                'note' => $customerPayment->note,
                'datetime' => $customerPayment->date,
                'created_by' => Auth::user()->id,
            ]);
        }
        
        $data->update($storeData);

        return $data;
    }

    public static function delete($id)
    {
        $data = Order::find($id);
        foreach ($data->images as $img) {
            if ($img->image) {
                MediaUploader::delete('orders', $img->image, true);
            }
        }
        $data->images()->delete();
        if (count($data->advance) > 0) {
            if ($data->advance->transactions()) {
                $data->advance->transactions()->delete();
            }
            $data->advance()->delete();
        }
        $data->items()->delete();
        $data->delete();

        return true;
    }
}
