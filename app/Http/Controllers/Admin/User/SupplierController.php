<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\CodeService;
use App\Services\SupplierService;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list supplier');
        
        $sql = Supplier::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where('name', 'LIKE', '%'. $request->q . '%')
            ->orWhere('code', 'LIKE', '%'. $request->q . '%')
            ->orWhere('contact_person', 'LIKE', '%'. $request->q . '%')
            ->orWhere('contact_no', 'LIKE', '%'. $request->q . '%')
            ->orWhere('opening_due', 'LIKE', '%'. $request->q . '%')
            ->orWhere('address', 'LIKE', '%'. $request->q . '%')
            ->orWhere('email', 'LIKE', '%'. $request->q . '%');
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->paginate($request->limit ?? config('settings.per_page_limit'));

        return view('admin.user.supplier', compact('records'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add supplier');
        return view('admin.user.supplier')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add supplier');
        $rules = [
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255|unique:suppliers,contact_no',
            'status' => 'required|in:Active,Deactivated',
        ];
        $rules['email'] = 'nullable|string|max:255|unique:suppliers,email';
        
        $this->validate($request, $rules);
        
        $code = CodeService::generateUserCode('Supplier',Supplier::class, 'code');
        
        $storeData = [
            'code' => $code,
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'opening_due' => $request->opening_due,
            'address' => $request->address,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ];
        
        Supplier::create($storeData);

        $request->session()->flash('successMessage', 'Supplier was successfully added!');
        return redirect()->route('admin.user.supplier.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('list supplier');

        $data = Supplier::findOrFail($id);
        return view('admin.user.supplier', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit supplier');
        $data = Supplier::findOrFail($id);
        return view('admin.user.supplier', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit supplier');

        $rules = [
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:255|unique:suppliers,contact_no,' . $id . ',id',
            'status' => 'required|in:Active,Deactivated',
        ];
        $rules['email'] = 'nullable|string|max:255|unique:suppliers,email,' . $id . ',id';

        $this->validate($request, $rules);

        $data = Supplier::findOrFail($id);

        $storeData = [
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'opening_due' => $request->opening_due,
            'address' => $request->address,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ];
        $data->update($storeData);

        $request->session()->flash('successMessage','Supplier was successfully updated!');
        return redirect()->route('admin.user.supplier.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        
        $this->authorize('delete supplier');
        $data = Supplier::findOrFail($id);
        $data->delete();
        
        $request->session()->flash('successMessage','Supplier was successfully deleted!');
        return redirect()->route('admin.user.supplier.index', qArray());
    }

    public function statusChange(Request $request, $id)
    {
        $this->authorize('edit supplier');

        $data = Supplier::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => auth()->user()->id]);

        $request->session()->flash('successMessage', 'Supplier status was successfully changed!');
        return redirect()->route('admin.user.supplier.index', qArray());
    }
    public function due(Request $request)
    {
        $due = SupplierService::due($request->id);
        return response()->json(['success' => true, 'due' => $due]);
    }
}