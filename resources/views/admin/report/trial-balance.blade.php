@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> {{ __('Trial Balance') }}
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.balance-sheet') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="date" id="datepickerFrom"
                                        value="{{ dbDateRetrieve(Request::get('date')) }}" placeholder="Till Date">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn-flat">{{ __('Search') }}</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.report.balance-sheet') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                                <tr>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-6">Stock</div>
                                            <div class="col-md-6 text-right">
                                                {{ number_format($closingRawMaterial + $closingFinished, 2) }}
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-6">Supplier Closing Balance</div>
                                            <div class="col-md-6 text-right">{{ number_format($supplierClosingBalance, 2) }}
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-6">Customer Closing Balance</div>
                                            <div class="col-md-6 text-right">
                                                {{ number_format($customerClosingBalance, 2) }}</div>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-6">Bank/Cash</div>
                                            <div class="col-md-6 text-right">{{ number_format($bankCash, 2) }}</div>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th>

                                        <div class="row">
                                            <div class="col-md-6">Profit/Loss</div>
                                            <div class="col-md-6 text-right">
                                                {{ number_format($profitLossAmount['grossProfit'], 2) }}</div>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th>

                                        <div class="row">
                                            <div class="col-md-6">Expense</div>
                                            <div class="col-md-6 text-right">
                                                {{ number_format($profitLossAmount['totalExpense'], 2) }}</div>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-6"></div>
                                            <div class="col-md-6"></div>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-6">Income</div>
                                            <div class="col-md-6 text-right">{{ number_format($profitLossAmount['totalIncome'], 2) }}</div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-6">Total</div>
                                            <div class="col-md-6 text-right">
                                                {{ number_format($closingRawMaterial + $closingFinished + $customerClosingBalance + $bankCash  + $profitLossAmount['totalExpense'], 2) }}/=
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="row">
                                            <div class="col-md-6"></div>
                                            <div class="col-md-6 text-right">
                                                {{ number_format($supplierClosingBalance+ $profitLossAmount['totalIncome']  + $profitLossAmount['grossProfit'], 2) }}/=
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
