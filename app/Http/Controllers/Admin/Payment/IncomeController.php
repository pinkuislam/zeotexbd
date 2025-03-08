<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Income;
use App\Models\IncomeItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\IncomeCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeRequest;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('list income');

        $sql = Income::with([
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
        
        $incomes = $sql->paginate($request->limit ?? 15);
        
        return view('admin.payment.income.index', compact('incomes'))->with('list', 1);
    }
    
    public function create()
    {
        $this->authorize('add income');

        $banks = Bank::select('id','bank_name')->where('status', 'Active')->get();
        $categories = IncomeCategory::select('id','name')->where('status', 'Active')->get();

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'income_category_id' => null,
                'amount' => null
            ]
        ];
        return view('admin.payment.income.index', compact('banks', 'categories','items'))->with('create', 1);
    }
    
    public function store(IncomeRequest $request)
    {
        $this->authorize('add income');

        $data = Income::create([
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'total_amount' => $request->total_amount,
            'created_by' => Auth::user()->id,
        ]);

        if ($request->only('income_item_id')) {
            $amount = 0;
            foreach ($request->income_item_id as $key => $eitem) {
               $item = IncomeItem::create([
                    'income_id' => $data->id,
                    'income_category_id' => $request->category_id[$key],
                    'bank_id' => $request->bank_id[$key],
                    'amount' => $request->amount[$key],
                ]);
                $amount += $request->amount[$key];
                if ($item) {
                    Transaction::create([
                        'type' => 'Received',
                        'flag' => 'Income',
                        'flagable_id' => $item->id,
                        'flagable_type' => IncomeItem::class,
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
        $request->session()->flash('successMessage', 'Income was successfully added!');
        return redirect()->route('admin.payment.income.create', qArray());
    }
    
    public function show(Request $request, $id)
    {
        $this->authorize('show income');

        $data = Income::with([
            'items', 
            'items.bank', 
            'items.category'
        ])->find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.income.index', qArray());
        }
        
        return view('admin.payment.income.index', compact('data'))->with('show', $id);
    }
    
    public function edit(Request $request, $id)
    {
        $this->authorize('edit income');

        $data = Income::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.income.index', qArray());
        }
        if (count($data->items) > 0) {
            $items = $data->items;
        }else {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'income_category_id' => null,
                    'amount' => null
                ]
            ];
        }
        $banks = Bank::select('id','bank_name')->where('status', 'Active')->get();
        $categories = IncomeCategory::select('id','name')->where('status', 'Active')->get();
        
        return view('admin.payment.income.index', compact('data', 'banks', 'categories','items'))->with('edit', $id);
    }
    
    public function update(IncomeRequest $request, $id)
    {
        $this->authorize('edit income');
        
        $data = Income::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.income.index', qArray());
        }
        
        $data->update([
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'total_amount' => $request->total_amount,
            'updated_by' => Auth::user()->id,
        ]);

        if (count($request->income_item_id) > 0) {
            $amount = 0;
            IncomeItem::whereNotIn('income_category_id', $request->category_id)
            ->where('income_id', $data->id)
            ->delete();
            foreach ($request->income_item_id as $key => $eitem) {
                if ($eitem > 0) {
                        IncomeItem::where('id', $eitem)->update([
                            'income_id' => $data->id,
                            'income_category_id' => $request->category_id[$key],
                            'bank_id' => $request->bank_id[$key],
                            'amount' => $request->amount[$key],
                        ]);
                    $item = IncomeItem::find($eitem);
                } else {
                    $item = IncomeItem::create([
                        'income_id' => $data->id,
                        'income_category_id' => $request->category_id[$key],
                        'bank_id' => $request->bank_id[$key],
                        'amount' => $request->amount[$key],
                    ]);
                }
                $amount += $request->amount[$key];
                if ($item) {
                    Transaction::updateOrCreate([
                        'flagable_id' => $item->id,
                        'flagable_type' => IncomeItem::class,
                    ], [
                        'type' => 'Received',
                        'flag' => 'Income',
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
        $request->session()->flash('successMessage', 'Income was successfully updated!');
        return redirect()->route('admin.payment.income.index', qArray());
    }
    
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete income');

        $data = Income::find($id);

        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('admin.payment.income.index', qArray());
        }
        foreach ($data->items as $item) {
            Transaction::where('flagable_id', $item->id)->where('flagable_type', IncomeItem::class)->delete();
            $item->delete();
        }
        $data->delete();
        
        $request->session()->flash('successMessage', 'Income was successfully deleted!');
        return redirect()->route('admin.payment.income.index', qArray());
    }
}
