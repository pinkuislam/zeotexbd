<?php

namespace App\Http\Controllers\Admin\User;

use App\Models\Investor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InvestorController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list investor');

        $sql = Investor::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('mobile', 'LIKE', $request->q.'%')
                ->orWhere('address', 'LIKE', $request->q.'%')
                ->orWhere('status', 'LIKE', $request->q.'%')
                ->orWhereHas('creator', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->q . '%');
                });
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $investors = $sql->paginate($request->limit ?? 15);

        return view('admin.user.investor', compact('investors'))->with('list', 1);
    }

    public function create()
    {
        $this->authorize('add investor');
        return view('admin.user.investor')->with('create', 1);
    }

    public function store(Request $request)
    {
        $this->authorize('add investor');

        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'nullable|max:255|unique:investors,mobile',
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
        Investor::create($storeData);

        $request->session()->flash('successMessage', 'Investor was successfully added!');
        return redirect()->route('admin.user.investor.create', qArray());
    }

    public function show(Request $request, $id)
    {
        $this->authorize('show investor');

        $data = Investor::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.user.investor.index', qArray());
        }

        return view('admin.user.investor', compact('data'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('edit investor');

        $data = Investor::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.user.investor.index', qArray());
        }

        return view('admin.user.investor', compact('data'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit investor');

        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'nullable|max:255|unique:investors,mobile,'.$id.',id',
            'address' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Investor::find($id);
        
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.user.investor.index', qArray());
        }

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);

        $request->session()->flash('successMessage', 'Investor was successfully updated!');
        return redirect()->route('admin.user.investor.index', qArray());
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete investor');

        $data = Investor::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.user.investor.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Investor was successfully deleted!');
        return redirect()->route('admin.user.investor.index', qArray());
    }
}
