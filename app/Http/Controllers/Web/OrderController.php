<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderImage;
use App\Models\EcommerceOrderItem;
use App\Models\ShippingRate;
use App\Services\CodeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MediaUploader;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'area' => 'required|integer',
            'images' => 'nullable|array',
            'images.*' => 'nullable|mimes:jpg,jpeg,png,gif,svg,webp',
        ]);
        DB::beginTransaction();
        try {
            $carts = session()->get('cart');
            $totatQty = 0;
            $code = CodeService::generate(EcommerceOrder::class, 'EO', 'serial_number');
            $shipping = ShippingRate::find($request->area);
            $storeData = [
                'serial_number' => $code,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'shipping_rate_id' => $shipping->id,
                'total_quantity' => $totatQty,
                'sub_total_amount' => $carts['subtotal'] ?? 0,
                'total_amount' => ($carts['subtotal'] + $shipping->rate) ,
            ];
            $order = EcommerceOrder::create($storeData);

            if (count($carts['items']) > 0) {
                foreach ($carts['items'] as $value) {
                    $totatQty += $value['quantity'];
                    EcommerceOrderItem::create([
                        'ecommerce_order_id' => $order->id,
                        'barcode' => $value['product']->barcode,
                        'quantity' =>  $value['quantity'],
                        'product_id' => $value['product']->product_id,
                        'color_id' => $value['product']->color_id,
                        'size_id' => $value['product']->size_id,
                        'old_price' => $value['product']->old_price ?? 0,
                        'sale_price' => $value['product']->sale_price ?? 0,
                        'amount' => ($value['product']->sale_price * $value['quantity']) ?? 0,
                    ]);
                }
                $order->update([
                    'total_quantity' => $totatQty,
                ]);
            }
            if (count($request->images) > 0) {
                foreach ($request->images as $key=>$image) {
                    $file = MediaUploader::imageUpload($request->images[$key], 'ecommerceorders', 1, null,null);
                    EcommerceOrderImage::create([
                        'ecommerce_order_id' => $order->id,
                        'image' => $file['name'],
                    ]);
                }
            }
            session()->forget('cart');
            DB::commit();
            $request->session()->flash('successMessage', 'Order saved successfully. Thank you for your order.');
            return redirect()->route('ecommerce.checkout.complete')->with('order', $order);
        } catch (Exception $e) {
            // dd($e);
            DB::rollBack();
            $request->session()->flash('errorMessage', 'Order saving failed. Sorry for inconvenience. Please try again.');
            return redirect()->back();
        }
    }
    public function orderTracking(Request $request)
    {
        if (!$request->id) {
            abort(404);
        }

        $data = EcommerceOrder::with(['tracks'])->where('serial_number', $request->id)->firstOrFail();
        $trackingStatus = $data->tracks->pluck('status')->toArray();
        // return view('ecom.checkout.order-tracking', compact('data', 'trackingStatus'));
    }
}
