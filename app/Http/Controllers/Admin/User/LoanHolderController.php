<?php

namespace App\Http\Controllers\Admin\User;

use App\Models\LoanHolder;
use Illuminate\Http\Request;
use App\Services\LoanHolderService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoanHolderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list loan-holder');

        $sql = LoanHolder::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('mobile', 'LIKE', $request->q.'%')
                ->orWhere('address', 'LIKE', $request->q.'%')
                ->orWhere('status', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $loanHolders = $sql->paginate($request->limit ?? 15);

        return view('admin.user.loan-holder', compact('loanHolders'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('create loan-holder');
        return view('admin.user.loan-holder')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add loan-holder');

        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'nullable|max:255|unique:loan_holders,mobile',
            'address' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        LoanHolder::create($storeData);

        $request->session()->flash('successMessage', 'Loan Holder was successfully added!');
        return redirect()->route('admin.user.loan-holder.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show loan-holder');

        $data = LoanHolder::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.user.loan-holder.index', qArray());
        }

        return view('admin.user.loan-holder', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit loan-holder');

        $data = LoanHolder::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.user.loan-holder.index', qArray());
        }

        return view('admin.user.loan-holder', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit loan-holder');

        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'nullable|max:255|unique:loan_holders,mobile,'.$id.',id',
            'address' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = LoanHolder::find($id);
        
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.user.loan-holder.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Loan Holder was successfully updated!');
        return redirect()->route('admin.user.loan-holder.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete loan-holder');

        $data = LoanHolder::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.user.loan-holder.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Loan Holder was successfully deleted!');
        return redirect()->route('admin.user.loan-holder.index', qArray());
    }
    
    public function due(Request $request)
    {
        $credentials = $request->only('id');
        $validator = Validator::make($credentials, [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(", " , $validator->messages()->all())], 200);
        }

        $due = LoanHolderService::due($request->id);

        return response()->json(['success' => true, 'due' => $due]);
    }
}
