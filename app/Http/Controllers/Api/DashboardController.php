<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResellerService;
use App\Services\SellerService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->role == 'Seller') {
            $due = SellerService::due(auth()->user()->id);
        }elseif (auth()->user()->role == 'Reseller') {
            $due = ResellerService::due(auth()->user()->id);
        }else {
            $due = 0;
        }
        if (auth()->user()->role == 'Seller' || auth()->user()->role == 'Reseller') {
           $data = [
                'type' =>auth()->user()->role,
                'name' =>auth()->user()->name,
                'phone' =>auth()->user()->mobile,
                'balance' => $due,
                'total_customer' => auth()->user()->totalCustomer(),
                'total_customer_due' => auth()->user()->totalCustomerDue(),
                'total_order' => auth()->user()->totalCustomerOrder(),
                'total_order_amount' => auth()->user()->totalCustomerOrderAmount(),
           ];
        }else {
            $data = [];
        }
        return response()->json([
            'status' => true,
            'message' => 'Dashboard Request Successfully.',
            'data' => $data,
        ]);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
}
