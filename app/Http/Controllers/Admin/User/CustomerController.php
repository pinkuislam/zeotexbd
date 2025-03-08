<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Customer;
use App\Models\ShippingRate;
use App\Traits\UserTrait;
use App\Models\User;
use App\Services\CustomerService;
use Exception;

class CustomerController extends Controller
{
    use UserTrait;
    public function index(Request $request)
    {
        $this->authorize('list customer');
        $records = CustomerService::index($request);
        return view('admin.user.customer', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add customer');
        $data['shipping_methods'] = ShippingRate::where('status','Active')->latest()->get(['id','name']);
        return view('admin.user.customer',$data)->with('create', 1);
    }

    public function store(CustomerRequest $request)
    {
        $this->authorize('add customer'); 
        try {
            CustomerService::store($request);
            $request->session()->flash('successMessage', 'Customer was successfully added!');
            return redirect()->route('admin.user.customer.create', qArray());
        } catch (Exception $e) {
            $request->session()->flash('errorMessage', $e->getMessage());
            return redirect()->route('admin.user.customer.create', qArray());
        }
    }

    public function show(Request $request, $id)
    {
        $this->authorize('list customer');

        $data = CustomerService::show($id); 
        return view('admin.user.customer', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit customer');
        $data['data'] = Customer::findOrFail($id);
        $data['users'] =$this->user($data['data']->type)->get(['id','name']);
        $data['shipping_methods'] = ShippingRate::where('status','Active')->latest()->get(['id','name']);
        return view('admin.user.customer', $data)->with('edit', $id);
    }

    public function update(CustomerRequest $request, $id)
    {
        $this->authorize('edit customer');
       try {
            CustomerService::update($request, $id); 
            $request->session()->flash('successMessage','Customer was successfully updated!');
            return redirect()->route('admin.user.customer.index', qArray());
        } catch (Exception $e) {
            $request->session()->flash('errorMessage', $e->getMessage());
            return back();
        }
    }

    public function destroy(Request $request, $id)
    {
        
        $this->authorize('delete customer');
        try {
            CustomerService::delete($id); 
            $request->session()->flash('successMessage','Customer was successfully deleted!');
            return redirect()->route('admin.user.customer.index', qArray());
        } catch (Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return back();
        }
       
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('edit customer');

        try {
            CustomerService::status($id); 
            $request->session()->flash('successMessage', 'Customer status was successfully changed!');
            return redirect()->route('admin.user.customer.index', qArray());
        } catch (Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return back();
        } 
    }

    public function import(Request $request)
    {
        $this->authorize('add customer');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new CustomerImport, $request->file);

        $request->session()->flash('successMessage', 'Customer was successfully imported!');
        return redirect()->route('admin.user.customer.index', qArray());
    }

    public function export(Request $request)
    {
        $this->authorize('list customer');

        return Excel::download(new CustomerExport, 'customers-'. time() . '.xlsx');
    }
    public function getCustomer(Request $request)
    {
        if ($request->id) {
            $res = Customer::where('status','Active')->where('user_id',$request->id)->get(['id','name','shipping_rate_id','mobile']);
        }else{
            $res = Customer::where('status','Active')->where('type','Admin')->get(['id','name','mobile']);
        }
        if ($res) {
            return response()->json(['success' => true, 'data' => $res]);
        }
        return response()->json(['success' => false]);
    }
    public function getSingleCustomer(Request $request)
    {
        $res = Customer::with('orders')->where('status','Active')->where('id',$request->id)->first();
        $due = CustomerService::due($request->id);
        if ($res) {
            return response()->json(['success' => true, 'data' => $res, 'due' => $due]);
        }
        return response()->json(['success' => false]);
    }
    public function due(Request $request)
    {
        $due = CustomerService::due($request->id);
        return response()->json(['success' => true, 'due' => $due]);
    }
    public function ajaxStore(CustomerRequest $request)
    {
       $this->authorize('add customer');
       $storeData = [
           'type' => $request->type,
           'name' => $request->name,
           'contact_name' => $request->contact_person,
           'mobile' => $request->mobile,
           'email' => $request->email,
           'address' => $request->address,
           'shipping_address' => $request->shipping_address,
           'shipping_rate_id' => $request->shipping_rate_id,
           'status' => $request->status,
           'created_by' => auth()->user()->id,
       ];
       if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')) {
           if ($request->type == "Admin") {
               $storeData['user_id'] = auth()->user()->id; 
           }else{
               $storeData['user_id'] = $request->user_id;
           }
           $storeData['type'] = $request->type;
       }else {
           $storeData['type'] =auth()->user()->role;
           $storeData['user_id'] = auth()->user()->id;
       }
       Customer::create($storeData);
        return response()->json(['success' => true, 'successMessage' => 'Customer was successfully added!']);
    }
    public function customerHistory($id)
    {
        $customer = Customer::with('orders')->find($id);
        return view('admin.user.customer-history', compact('customer'));
    }
}