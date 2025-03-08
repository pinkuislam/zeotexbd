<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerCollection;
use App\Models\Customer;
use App\Services\CustomerService;
use Exception;

class CustomerController extends Controller
{

    /**
     * @authenticated
     * @responseFile responses/user/customer.json
     */
    public function index()
    {
        try {
            if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
                $data = Customer::where('status','Active')->get();
            }else{
                $data = Customer::where('user_id', auth()->user()->id)->where('status','Active')->get();
            }
            return response()->json([
                'status' => true,
                'message' => 'Customers Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
        
    }

    public function store(CustomerRequest $request)
    {
        try {
            $storeData = [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'address' => $request->address,
                'shipping_address' => $request->address,
                'status' => 'Active',
                'type' => auth()->user()->role,
                'user_id' => auth()->user()->id,
                'created_by' => auth()->user()->id,
            ];
            $data = Customer::create($storeData);
            return response()->json([
                'status' => true,
                'message' => 'Customer Store Successfully.',
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
     * @responseFile responses/user/single-customer.json
     */
    public function show($id)
    {
        try {
            $data = CustomerService::show($id);
            return response()->json([
                'status' => true,
                'message' => 'Customer Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(CustomerRequest $request, $id)
    {
        try {
            $data = Customer::findOrFail($id);
            $storeData = [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'address' => $request->address,
                'shipping_address' => $request->address,
                'status' => 'Active',
                'type' => auth()->user()->role,
                'user_id' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];
            $data->update($storeData);
            $data = CustomerService::update($request, $id);
            return response()->json([
                'status' => true,
                'message' => 'Customer Update Request Successfully.',
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
     * @Method DELETE
     * @response
     *  {
      *  "status": true,
      *  "message": "Customer was successfully deleted."
     *   }
     */
    public function destroy($id)
    {
        try {
            CustomerService::delete($id);
            return response()->json([
                'status' => true,
                'message' => 'Customer was successfully deleted.'
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
     * @Method PUT
     * @responseFile responses/user/customer-status.json
     */
    public function status($id)
    {
        try {
            $data = CustomerService::status($id);
            return response()->json([
                'status' => true,
                'message' => 'Customer status Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function customerHistory($mobile)
    {
        try {
            $data = Customer::with('orders')->where('mobile', 'LIKE', '%'. $mobile . '%' )->first();
            return response()->json([
                'status' => true,
                'found' => $data ? 'Yes' : 'No',
                'my_customer' => ($data && $data->user_id  == auth()->user()->id) ? 'Yes' : 'No',
                'message' => 'Customer History Request Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function addToMyCustomer($id)
    {
        try {
            $data = Customer::findOrFail($id);
            $storeData = [
                'name' => $data->name,
                'contact_name' => $data->contact_person,
                'mobile' => $data->mobile,
                'email' => $data->email,
                'address' => $data->address,
                'shipping_address' => $data->shipping_address,
                'shipping_rate_id' => $data->shipping_rate_id,
                'status' => $data->status,
                'type' => auth()->user()->role,
                'user_id' => auth()->user()->id,
                'created_by' => auth()->user()->id,
            ];
            $data= Customer::create($storeData);
            return response()->json([
                'status' => true,
                'message' => 'Customer Created  Successfully.',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
