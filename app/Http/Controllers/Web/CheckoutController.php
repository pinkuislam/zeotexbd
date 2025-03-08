<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;

class CheckoutController extends Controller
{
    public function index()
    {
        $shippings = ShippingRate::where('status','Active')->get();
        return inertia('checkout/Index', compact('shippings'));
    }
    public function completeOrder()
    {
        $order = session()->get('order');
        return inertia('checkout/Complete', compact('order'));
    }
}
