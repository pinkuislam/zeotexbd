<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\ProductUse;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SaleService
{

    public static function store($request)
    { 
        $storeData = [
            'code' => $request->code,
            'invoice_number' => $request->code,
            'date' => dbDateFormat($request->date),
            'order_id' => $request->order_id,
            'user_id' => $request->user_id ?? auth()->user()->id,
            'type' => $request->type ?? auth()->user()->role,
            'customer_id' => $request->customer_id,
            'reseller_business_id' => $request->reseller_business_id,
            'delivery_agent_id' => $request->delivery_agent_id,
            'shipping_charge' => $request->shipping_charge,
            'vat_percent' => $request->vat_percent ?? 0,
            'vat_amount' => $request->vat_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'advance_amount' => $request->advance_amount ?? 0,
            'subtotal_amount' => $request->sub_total_amount,
            'total_amount' => $request->total_amount,
            'note' => $request->note,
            'status' => 'Processing',
            'created_by' => auth()->user()->id
        ];
        if ($storeData['type'] == "Seller") {
            $storeData['commission_percent'] =  config('app.seller_commision');
            $storeData['commission_amount'] =  $request->sub_total_amount * config('app.seller_commision') / 100;
        }

        //TODO::
        
        $storeData['commission_percent'] =  0;
        $storeData['commission_amount'] =  0;
        if($request->user_id){
            $user = User::find($request->user_id);
        }


        $data = Sale::create($storeData);

        $order = Order::find($request->order_id);
        $order->update([
            'status' => 'Processing'
        ]);
        if ($request->only('product_id')) {
            $resellerAmount = 0;
            foreach ($request->product_id as $key => $row) {
                $product = Product::findOrFail($request->product_id[$key]);
                $stock = 99999999;
                if($product->product_type == 'Combo'){
                    foreach($product->items as $baseItem){
                        $stockIn = ProductIn::where('product_id', $baseItem->base_id)->where('color_id', $request->color_id[$key])->sum('quantity');
                        $stockOut = ProductIn::where('product_id', $baseItem->base_id)->where('color_id', $request->color_id[$key])->sum('used_quantity');
                        $baseStock = $stockIn - $stockOut;
                        if($baseStock < $stock){
                            $stock = $baseStock;
                        }
                    }

                } else {
                    $stockIn = ProductIn::where('product_id', $product->id)->where('color_id', $request->color_id[$key])->sum('quantity');
                    $stockOut = ProductIn::where('product_id', $product->id)->where('color_id', $request->color_id[$key])->sum('used_quantity');
                    $stock = $stockIn - $stockOut;
                }
                
                if( $request->quantity[$key] > 0 && $stock >= $request->quantity[$key]){
                    $resellerPrice = 0;
                    if ($storeData['type'] == "Reseller") {
                        $resellerPrice = $user->userProducts->find($product->id);
                        
                        $resellerAmount += $resellerPrice->pivot->price * $request->quantity[$key];
                    }

                    $saleItemData = [
                        'sale_id' => $data->id,
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key] ,
                        'color_id' => $request->color_id[$key] ,
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'reseller_unit_price' => $resellerPrice,
                        'amount' => ($request->unit_price[$key] * $request->quantity[$key]),
                    ];
                    $saleItem = SaleItem::create($saleItemData);

                    if($product->product_type == 'Combo') {
                        $comboPrice = 0;
                        foreach($product->items as $baseItem) {
                            $comboPrice += $baseItem->product->sale_price;
                        }
                        foreach($product->items as $baseItem) {
                            $price = $request->unit_price[$key] / $comboPrice * $baseItem->product->sale_price;
                            $discount = $data->discount_amount / $data->subtotal_amount * $price;
                            
                            $storeDataOut = [
                                'type' => "Sale",
                                'flagable_id' => $saleItem->id,
                                'flagable_type' => SaleItem::class,
                                'product_id' => $baseItem->base_id,
                                'unit_id' => $baseItem->product->unit_id ,
                                'color_id' => $request->color_id[$key] ,
                                'quantity' => $request->quantity[$key] * $baseItem->quantity,
                                'unit_price' => $price,
                                'net_unit_price' => $price - $discount,
                                'total_price' => ($price * $request->quantity[$key]),
                                'created_at' => now(),
                            ];
                        
                            $fgOutItem = ProductOut::create($storeDataOut);
                            $stockList = ProductIn::where('product_id', $baseItem->base_id)->where('color_id', $request->color_id[$key])->where('status', 0)->orderBy('id', 'asc')->get();
                            $soldQuantity = 0;
                            foreach ($stockList as $value) {
                                $remainingQuantity = $request->quantity[$key] * $baseItem->quantity - $soldQuantity;
                                $availableQuantity = $value->quantity - $value->used_quantity;
                                if ($remainingQuantity >= $availableQuantity) {
                                    ProductUse::create([
                                        'product_out_id' => $fgOutItem->id,
                                        'product_in_id' => $value->id,
                                        'quantity' => $availableQuantity
                                    ]);
        
                                    ProductIn::where('id', $value->id)->update([
                                        'status' => 1,
                                        'used_quantity' => $value->used_quantity + $availableQuantity
                                    ]);
        
                                    $soldQuantity += $availableQuantity;
                                } else {
        
                                    ProductUse::create([
                                        'product_out_id' => $fgOutItem->id,
                                        'product_in_id' => $value->id,
                                        'quantity' => $remainingQuantity
                                    ]);
        
                                    ProductIn::where('id', $value->id)->update([
                                        'status' => 0,
                                        'used_quantity' => $value->used_quantity + $remainingQuantity
                                    ]);
                                    $soldQuantity += $remainingQuantity;
        
                                    break;
                                }
                            }
                            if ($soldQuantity < $request->quantity[$key]) {
                                DB::rollBack();
                                $request->session()->flash('errorMessage', 'Insufficient stock');
                                return false;
                            }
                        }
                        
                    } else {
                        $discount = $data->discount_amount / $data->subtotal_amount * $request->unit_price[$key];
                        $storeDataOut = [
                            'type' => "Sale",
                            'flagable_id' => $saleItem->id,
                            'flagable_type' => SaleItem::class,
                            'product_id' => $request->product_id[$key],
                            'unit_id' => $request->unit_id[$key] ,
                            'color_id' => $request->color_id[$key] ,
                            'quantity' => $request->quantity[$key],
                            'unit_price' => $request->unit_price[$key],
                            'net_unit_price' => $request->unit_price[$key] - $discount,
                            'total_price' => ($request->unit_price[$key] * $request->quantity[$key]),
                            'created_at' => now(),
                        ];
                    
                        $fgOutItem = ProductOut::create($storeDataOut);
                        $stockList = ProductIn::where('product_id', $product->id)->where('color_id', $request->color_id[$key])->where('status', 0)->orderBy('id', 'asc')->get();
                        $soldQuantity = 0;
                        foreach ($stockList as $value) {
                            $remainingQuantity = $request->quantity[$key] - $soldQuantity;
                            $availableQuantity = $value->quantity - $value->used_quantity;
                            if ($remainingQuantity >= $availableQuantity) {
                                ProductUse::create([
                                    'product_out_id' => $fgOutItem->id,
                                    'product_in_id' => $value->id,
                                    'quantity' => $availableQuantity
                                ]);

                                ProductIn::where('id', $value->id)->update([
                                    'status' => 1,
                                    'used_quantity' => $value->used_quantity + $availableQuantity
                                ]);

                                $soldQuantity += $availableQuantity;
                            } else {

                                ProductUse::create([
                                    'product_out_id' => $fgOutItem->id,
                                    'product_in_id' => $value->id,
                                    'quantity' => $remainingQuantity
                                ]);

                                ProductIn::where('id', $value->id)->update([
                                    'status' => 0,
                                    'used_quantity' => $value->used_quantity + $remainingQuantity
                                ]);
                                $soldQuantity += $remainingQuantity;

                                break;
                            }
                        }
                        if ($soldQuantity < $request->quantity[$key]) {
                            DB::rollBack();
                            $request->session()->flash('errorMessage', 'Insufficient stock');
                            return false;
                        }
                    }
                    
                } else{
                    DB::rollBack();
                    $request->session()->flash('errorMessage', 'Insufficient stock');
                    return false;
                }

            }

            if ($storeData['type'] == "Reseller") {
                $data->update([
                    'reseller_amount' => $data->subtotal_amount - $resellerAmount
                ]);
            }

        }

      return $data;
    }

    public static function update($request, $id)
    {
        $data = Sale::with('items')->find($id);
        $updateData = [
            'date' => dbDateFormat($request->date),
            'delivery_agent_id' => $request->delivery_agent_id,
            'vat_percent' => $request->vat_percent ?? 0,
            'vat_amount' => $request->vat_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'subtotal_amount' => $request->sub_total_amount,
            'total_amount' => $request->total_amount,
            'note' => $request->note,
            'updated_by' => auth()->user()->id
        ];
        
        if($data->discount_amount != $request->discount_amount){
            foreach($data->items as $item){
                if($item->product->product_type == 'Combo'){
                    $comboPrice = 0;
                    foreach($item->product->items as $baseItem) {
                        $comboPrice += $baseItem->product->sale_price;
                    }
                    foreach($item->items as $out){
                        $price = $item->unit_price / $comboPrice * $out->product->sale_price;
                        $discount = $data->discount_amount / $data->subtotal_amount * $price;
                        $out->update([
                            'unit_price' => $price,
                            'net_unit_price' => $price - $discount,
                            'total_price' => $price * $item->quantity,
                            'updated_by' => auth()->user()->id]);
                    }
                } else{
                    foreach($item->items as $out){
                        $discount = $data->discount_amount / $data->subtotal_amount * $out->unit_price;
                        $out->update([
                            'net_unit_price' => $out->unit_price - $discount,
                            'updated_by' => auth()->user()->id]);
                    }
                }
            }
        }

        $data->update($updateData);
        return $data;
    }

    public static function delete($id)
    {
        $data = Sale::find($id);
        $fgUsages = ProductUse::where('product_outs.flagable_id', $id)->where('product_outs.flagable_type', Sale::class)
        ->join('product_outs', 'product_uses.product_out_id', '=', 'product_outs.id')
        ->get();

        foreach($fgUsages as $value){
            $info = ProductIn::find($value->product_in_id);
            $info->update(['used_quantity'=>($info->used_quantity - $value->quantity), 'status'=>0]);
        }

        $usesIds = $fgUsages->pluck('id');

        ProductUse::whereIn('id', $usesIds)->delete();
        ProductOut::where('flagable_id', $id)->where('flagable_type', Sale::class)->delete();
        if ($data->order_id) {
            $order = Order::find($data->order_id);
            $order->update([
                'status' => 'Ordered'
            ]);
        }
        $data->delete();
        return true;
    }
}