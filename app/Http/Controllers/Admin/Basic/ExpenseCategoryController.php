<?php

namespace App\Http\Controllers\Admin\Basic;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list expense_category');

        $sql = ExpenseCategory::orderBy('name', 'ASC');
        
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
        
        return view('admin.expense.category', compact('categories'))->with('list', 1);
    }
    
    public function create()
    {
        $this->authorize('add expense_category');
        return view('admin.expense.category')->with('create', 1);
    }
    
    public function store(Request $request)
    {
        $this->authorize('add expense_category');

        $this->validate($request, [
            'name' => 'required|max:255|unique:expense_categories,name',
            'status' => 'required|in:Active,Inactive',
        ]);
        
        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        ExpenseCategory::create($storeData);
        
        $request->session()->flash('successMessage', 'Category was successfully added!');
        return redirect()->route('admin.basic.expense-category.create', qArray());
    }
    
    public function show(Request $request, $id)
    {
        $this->authorize('show expense_category');

        $data = ExpenseCategory::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.expense-category.index', qArray());
        }
        
        return view('admin.expense.category', compact('data'))->with('show', $id);
    }
    
    public function edit(Request $request, $id)
    {
        $this->authorize('edit expense_category');

        $data = ExpenseCategory::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.expense-category.index', qArray());
        }
        
        return view('admin.expense.category', compact('data'))->with('edit', $id);
    }
    
    public function update(Request $request, $id)
    {
        $this->authorize('edit expense_category');

        $this->validate($request, [
            'name' => 'required|max:255|unique:expense_categories,name,'.$id.',id',
            'status' => 'required|in:Active,Inactive',
        ]);
        
        $data = ExpenseCategory::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.expense-category.index', qArray());
        }
        
        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];
        
        $data->update($storeData);
        
        $request->session()->flash('successMessage', 'Category was successfully updated!');
        return redirect()->route('admin.basic.expense-category.index', qArray());
    }
    
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete expense_category');

        $data = ExpenseCategory::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.basic.expense-category.index', qArray());
        }

        $data->delete();
        
        $request->session()->flash('successMessage', 'Category was successfully deleted!');
        return redirect()->route('admin.basic.expense-category.index', qArray());
    }
}
