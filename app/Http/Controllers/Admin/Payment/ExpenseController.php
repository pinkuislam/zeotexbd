<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExpenseRequest;

class ExpenseController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list expense');
        $sql = Expense::with([
            'items', 
            'items.bank', 
            'items.category'
        ])->orderBy('id', 'DESC');
        
        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('note', 'LIKE','%'. $request->q . '%')
                ->orWhere('date', 'LIKE','%'. $request->q . '%')
                ->orWhere('total_amount', 'LIKE','%'. $request->q . '%');
            });
            $sql->orwhereHas('items.category', function($q) use($request) {
                $q->where('name', 'LIKE','%'. $request->q . '%');
            });
            $sql->orwhereHas('items.bank', function($q) use($request) {
                $q->where('bank_name', 'LIKE','%'. $request->q . '%');
            });
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }
        
        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }
        
        $expenses = $sql->paginate($request->limit ?? 15);
        
        return view('admin.payment.expense.index', compact('expenses'))->with('list', 1);
    }
    
    public function create()
    {
        $this->authorize('add expense');

        $banks = Bank::select('id','bank_name')->where('status', 'Active')->get();
        $categories = ExpenseCategory::select('id','name')->where('status', 'Active')->get();

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'expense_category_id' => null,
                'amount' => null
            ]
        ];
        return view('admin.payment.expense.index', compact('banks', 'categories','items'))->with('create', 1);
    }
    
    public function store(ExpenseRequest $request)
    {
        $this->authorize('add expense'); 

        $data = Expense::create([
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'total_amount' => $request->total_amount,
            'created_by' => Auth::user()->id,
        ]);

        if ($request->only('expense_item_id')) {
            $amount = 0;
            foreach ($request->expense_item_id as $key => $eitem) {
               $item = ExpenseItem::create([
                    'expense_id' => $data->id,
                    'expense_category_id' => $request->category_id[$key],
                    'bank_id' => $request->bank_id[$key],
                    'amount' => $request->amount[$key],
                ]);
                $amount += $request->amount[$key];
                if ($item) {
                    Transaction::create([
                        'type' => 'Payment',
                        'flag' => 'Expense',
                        'flagable_id' => $item->id,
                        'flagable_type' => ExpenseItem::class,
                        'bank_id' => $item->bank_id,
                        'datetime' => now(),
                        'note' => $data->note,
                        'amount' => $item->amount,
                    ]);
                }
            }
            $data->update([
                'total_amount' => $amount
            ]);
        }
        $request->session()->flash('successMessage', 'Expense was successfully added!');
        return redirect()->route('admin.payment.expense.create', qArray());
    }
    
    public function show(Request $request, $id)
    {
        $this->authorize('show expense');

        $data = Expense::with([
            'items', 
            'items.bank', 
            'items.category'
        ])->find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.expense.index', qArray());
        }
        
        return view('admin.payment.expense.index', compact('data'))->with('show', $id);
    }
    
    public function edit(Request $request, $id)
    {
        $this->authorize('edit expense');

        $data = Expense::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.expense.index', qArray());
        }
        if (count($data->items) > 0) {
            $items = $data->items;
        }else {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'expense_category_id' => null,
                    'amount' => null
                ]
            ];
        }
        $banks = Bank::select('id','bank_name')->where('status', 'Active')->get();
        $categories = ExpenseCategory::select('id','name')->where('status', 'Active')->get();
        
        return view('admin.payment.expense.index', compact('data', 'banks', 'categories','items'))->with('edit', $id);
    }
    
    public function update(ExpenseRequest $request, $id)
    {
        $this->authorize('edit expense');
        
        $data = Expense::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.expense.index', qArray());
        }
        
        $data->update([
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'total_amount' => $request->total_amount,
            'updated_by' => Auth::user()->id,
        ]);

        if (count($request->expense_item_id) > 0) {
            $amount = 0;
            ExpenseItem::whereNotIn('expense_category_id', $request->category_id)
            ->where('expense_id', $data->id)
            ->delete();
            foreach ($request->expense_item_id as $key => $eitem) {
                if ($eitem > 0) {
                        ExpenseItem::where('id', $eitem)->update([
                            'expense_id' => $data->id,
                            'expense_category_id' => $request->category_id[$key],
                            'bank_id' => $request->bank_id[$key],
                            'amount' => $request->amount[$key],
                        ]);
                    $item = ExpenseItem::find($eitem);
                } else {
                    $item = ExpenseItem::create([
                        'expense_id' => $data->id,
                        'expense_category_id' => $request->category_id[$key],
                        'bank_id' => $request->bank_id[$key],
                        'amount' => $request->amount[$key],
                    ]);
                }
                $amount += $request->amount[$key];
                if ($item) {
                    Transaction::updateOrCreate([
                        'flagable_id' => $item->id,
                        'flagable_type' => ExpenseItem::class,
                    ], [
                        'type' => 'Payment',
                        'flag' => 'Expense',
                        'bank_id' => $item->bank_id,
                        'datetime' => now(),
                        'note' => $data->note,
                        'amount' => $item->amount,
                    ]);
                }
            }
            $data->update([
                'total_amount' => $amount
            ]);
   
        }
        $request->session()->flash('successMessage', 'Expense was successfully updated!');
        return redirect()->route('admin.payment.expense.index', qArray());
    }
    
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete expense');

        $data = Expense::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.expense.index', qArray());
        }
        foreach ($data->items as $item) {
            Transaction::where('flagable_id', $item->id)->where('flagable_type', ExpenseItem::class)->delete();
            $item->delete();
        }
        $data->delete();
        
        $request->session()->flash('successMessage', 'Expense was successfully deleted!');
        return redirect()->route('admin.payment.expense.index', qArray());
    }
}
