<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        if ($id = $request->input('tracking_number')) {
            return redirect()->action([self::class, 'show'], $id);
        }
        return inertia('tracking/Index');
    }

    public function show($id)
    {
        $trackingOrder = EcommerceOrder::where('serial_number', $id)->first();

        return inertia('tracking/Index', compact('trackingOrder'));
    }
}
