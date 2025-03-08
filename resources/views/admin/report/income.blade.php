@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> {{ __('income') }} {{ __('Reports') }}
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.income') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select name="income_expense_id" class="form-control select2">
                                        <option value="0">All Heads</option>
                                        @foreach ($incomeExpense as $value)
                                            <option value="{{ $value->id }}"
                                                {{ Request::get('income_expense_id') == $value->id ? 'selected' : '' }}>
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="bank_id" class="form-control select2">
                                        <option value="0">All Banks</option>
                                        @foreach ($banks as $value)
                                            <option value="{{ $value->id }}"
                                                {{ Request::get('bank_id') == $value->id ? 'selected' : '' }}>
                                                {{ $value->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="from" id="datepickerFrom"
                                        value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="to" id="datepickerTo"
                                        value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                                </div>

                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.report.income') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Date</th>
                                    <th>Bank</th>
                                    <th>Note</th>
                                    <th>Category</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $val)
                                    <tr>
                                        <td>{{ $val->income_number }}</td>
                                        <td>{{ $val->date }}</td>
                                        <td>{{ $val->bank_name . ' - ' . $val->account_no }}</td>
                                        <td>{{ $val->note }}</td>
                                        <td>{{ $val->incomeExpenseName }}</td>
                                        <td style="text-align:right">{{ number_format($val->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align:right;">Total</th>
                                    <th style="text-align:right;">{{ number_format($reports->sum('amount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
