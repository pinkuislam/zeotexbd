<?php

namespace App\Http\Controllers\Admin\Basic;

use Illuminate\Http\Request;
use App\Exports\ShippingRateExport;
use App\Imports\ShippingRateImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ShippingRate;

class ShippingMethodController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list shipping_method');

        $sql = ShippingRate::orderBy('created_at', 'DESC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', '%'. $request->q.'%')
                    ->orWhere('rate', 'LIKE', '%'. $request->q.'%')
                    ->orWhere('area', 'LIKE', '%'. $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }
        $data['shipping_methods'] = $sql->get();

        return view('admin.basic.shipping_method', $data)->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add shipping_method');
        return view('admin.basic.shipping_method')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add shipping_method');
        $this->validate($request, [
            'name' => 'required|max:255|unique:shipping_rates,name',
            'area' => 'required|max:255|string',
            'rate' => 'required|numeric',
            'note' => 'nullable|max:255|string',
            'status' => 'required|in:Active,Deactivated'
        ]);
        $storeData = [
            'name' => $request->name,
            'area' => $request->area,
            'rate' => $request->rate ?? 0.00,
            'note' => $request->note,
            'status' => $request->status,
            'created_by' => auth()->user()->id
        ];
        $data = ShippingRate::create($storeData);
        $request->session()->flash('successMessage', 'Shipping Method was successfully added!');
        return redirect()->route('admin.basic.shipping_method.create', qArray());
    }



    public function show(Request $request, $id)
    {
        $this->authorize('show shipping_method');
        $data = ShippingRate::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.shipping_method.index', qArray());
        }
        return view('admin.basic.shipping_method', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit shipping_method');
        $shipping = ShippingRate::find($id);
        if (empty($shipping)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.shipping_method.index', qArray());
        }
        $data['data'] = $shipping;
        return view('admin.basic.shipping_method', $data)->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit shipping_method');

        $this->validate($request, [
            'name' => 'required|max:255|unique:shipping_rates,name,'.$id.',id',
            'area' => 'required|max:255|string',
            'rate' => 'required|numeric',
            'note' => 'nullable|max:255|string',
            'status' => 'required|in:Active,Deactivated'
        ]);

        $data = ShippingRate::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.shipping_method.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'area' => $request->area,
            'rate' => $request->rate,
            'note' => $request->note,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Shipping Method was successfully updated!');
        return redirect()->route('admin.basic.shipping_method.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete shipping_method');
        $data = ShippingRate::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.shipping_method.index', qArray());
        }
        $data->delete();

        $request->session()->flash('successMessage', 'Shipping Method was successfully deleted!');
        return redirect()->route('admin.basic.shipping_method.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('status shipping_method');

        $data = ShippingRate::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => auth()->user()->id]);

        $request->session()->flash('successMessage', 'Shipping Method status was successfully changed!');
        return redirect()->route('admin.basic.shipping_method.index', qArray());
    }

    public function import(Request $request)
    {
        $this->authorize('add shipping_method');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx',
        ]);

        Excel::import(new ShippingRateImport, $request->file);

        $request->session()->flash('successMessage', 'Shipping Method was successfully imported!');
        return redirect()->route('admin.basic.shipping_method.index', qArray());
    }

    public function export(Request $request)
    {
        $this->authorize('list shipping_method');

        return Excel::download(new ShippingRateExport, 'shipping_method-'. time() . '.xlsx');
    }
    public function shippingCharge(Request $request)
    {
        $this->authorize('list shipping_method');

        $res = ShippingRate::findOrFail($request->id);
        if ($res) {
            return response()->json(['success' => true, 'data' => $res]);
        }
        return response()->json(['success' => false]);
    }
}
