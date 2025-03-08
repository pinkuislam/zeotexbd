@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <div class="tab-content">
            <div class="tab-pane active">
                <form method="GET" action="{{ route('admin.report.income-statement') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <input type="text" class="form-control" id="datepickerFrom" name="from" value="{{ Request::get('from') }}" placeholder="Date From">
                            </div>
                            
                            <div class="form-group">
                                <input type="text" class="form-control" id="datepickerTo" name="to" value="{{ Request::get('to') }}" placeholder="Date To">
                            </div>

                            <div class="form-group">
                                <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                <button type="submit"  name="action" value="search" class="btn btn-custom btn-flat">Search</button>
                                <a class="btn btn-custom btn-flat" href="{{ route('admin.report.income-statement') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Particulars</th>
                                        <th class="text-right">Debit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>COGS</td>
                                        {{-- <td class="text-right" title="Sales item er purchace er dealer price">{{ $cogs }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td>Cost of Sales Return</td>
                                        {{-- <td class="text-right" title="Sales Return item er Purchase price">{{ $saleReturnCost }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td>Cost of Purchase Return</td>
                                        {{-- <td class="text-right" title="Purchase Return item er purchace er dealer price">{{ $stockReturnCost }}</td> --}}
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Net Purchase</th>
                                        {{-- <th class="text-right">{{ $netStock = numberFormat($cogs - $saleReturnCost + $stockReturnCost) }}</th> --}}
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Particulars</th>
                                        <th class="text-right">Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Sales Amount</td>
                                        <td class="text-right" title="Net Sales Amount">{{ $salesAmount }}</td>
                                    </tr>
                                    <tr>
                                        <td>Sales Return Amount</td>
                                        {{-- <td class="text-right" title="Net Sales Return Amount">{{ $saleReturnsAmount }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td>Purchase Return Amount</td>
                                        {{-- <td class="text-right" title="Net Purchase Return Amount">{{ $stockReturnsAmount }}</td> --}}
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Net Sales</th>
                                        {{-- <th class="text-right">{{ $netSale = numberFormat($salesAmount - $saleReturnsAmount + $stockReturnsAmount) }}</th> --}}
                                    </tr>
                                    <tr>
                                        <th>Gross Profit</th>
                                        {{-- <th class="text-right">{{ $grossProfit = numberFormat($netSale - $netStock) }}</th> --}}
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="2">Expense Head</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $item)
                                    <tr>
                                        <td><a target="_blank" href="{{ route('reports.expenses') . '?category='. $item->category->id .'&from=' .Request::get('from') .'&to=' .Request::get('to') .'&limit=all' }}">{{ $item->category->name ?? '-' }}</a></td>
                                        <td class="text-right">{{ $item->amount }}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td>Customer Adj.</td>
                                        {{-- <td class="text-right">{{ $customerAdj->sum('amount') }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td>Bank Cost</td>
                                        {{-- <td class="text-right">{{ $bankPaymentCost }}</td> --}}
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total:</th>
                                        {{-- <th class="text-right">{{ $expenseTotal = ($expenses->sum('amount') + $customerAdj->sum('amount')) }}</th> --}}
                                        <th class="text-right">{{ $expenseTotal = ($expenses->sum('amount')) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="2">Income Head Head</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($incomes as $item)
                                    <tr>
                                        <td><a target="_blank" href="{{ route('reports.incomes') . '?category='. $item->category->id .'&from=' .Request::get('from') .'&to=' .Request::get('to') .'&limit=all' }}">{{ $item->category->name ?? '-' }}</a></td>
                                        <td class="text-right">{{ $item->amount }}</td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td>Supplier Adj.</td>
                                        {{-- <td class="text-right">{{ $supplierAdj->sum('amount') }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td>Bank Cost</td>
                                        {{-- <td class="text-right">{{ $bankReceivedCost }}</td> --}}
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total:</th>
                                        {{-- <th class="text-right">{{ $incomeTotal = numberFormat($grossProfit + $incomes->sum('amount') + $supplierAdj->sum('amount')) }}</th> --}}
                                        <th class="text-right">{{ $incomeTotal = numberFormat($incomes->sum('amount')) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Net Profit:</th>
                                        <th class="text-right">{{ numberFormat($incomeTotal - $expenseTotal) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
