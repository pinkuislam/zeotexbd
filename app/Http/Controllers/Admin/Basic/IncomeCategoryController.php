<?php

namespace App\Http\Controllers\Admin\Basic;

use Illuminate\Http\Request;
use App\Models\IncomeCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IncomeCategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list income_category');

        $sql = IncomeCategory::orderBy('name', 'ASC');
        
        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                ->orWhere('status', 'LIKE', '%' . $request->q . '%')
                ->orWhereHas('creator', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->q . '%');
                });
            });
        }
        
        if ($request->status) {
            $sql->where('status', $request->status);
        }
        
        $categories = $sql->paginate($request->limit ?? 15);
        
        return view('admin.income.category', compact('categories'))->with('list', 1);
    }
    
    public function create()
    {
        $this->authorize('add income_category');
        return view('admin.income.category')->with('create', 1);
    }
    
    public function store(Request $request)
    {
        $this->authorize('add income_category');

        $this->validate($request, [
            'name' => 'required|max:255|unique:income_categories,name',
            'status' => 'required|in:Active,Inactive',
        ]);
        
        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        IncomeCategory::create($storeData);
        
        $request->session()->flash('successMessage', 'Category was successfully added!');
        return redirect()->route('admin.basic.income-category.create', qArray());
    }
    
    public function show(Request $request, $id)
    {
        $this->authorize('show income_category');

        $data = IncomeCategory::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.income-category.index', qArray());
        }
        
        return view('admin.income.category', compact('data'))->with('show', $id);
    }
    
    public function edit(Request $request, $id)
    {
        $this->authorize('edit income_category');

        $data = IncomeCategory::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.income-category.index', qArray());
        }
        
        return view('admin.income.category', compact('data'))->with('edit', $id);
    }
    
    public function update(Request $request, $id)
    {
        $this->authorize('edit income_category');

        $this->validate($request, [
            'name' => 'required|max:255|unique:income_categories,name,'.$id.',id',
            'status' => 'required|in:Active,Inactive',
        ]);
        
        $data = IncomeCategory::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.income-category.index', qArray());
        }
        
        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];
        
        $data->update($storeData);
        
        $request->session()->flash('successMessage', 'Category was successfully updated!');
        return redirect()->route('admin.basic.income-category.index', qArray());
    }
    
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete income_category');

        $data = IncomeCategory::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.income-category.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Category was successfully deleted!');
        return redirect()->route('admin.basic.income-category.index', qArray());
    }
}
