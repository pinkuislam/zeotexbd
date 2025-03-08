@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Reseller Ledger
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.report.reseller-transactions') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Reseller Ledger Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.reseller') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="from" id="datepickerFrom"
                                        value="{{ Request::get('from') }}" placeholder="From Date">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="to" id="datepickerTo"
                                        value="{{ Request::get('to') }}" placeholder="To Date">
                                </div>
                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.report.reseller') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Mobile</th>
                                    <th>Orders</th>
                                    <th>Sales Comfirm</th>
                                    <th>Sales Amount</th>
                                    <th>Sale Returns</th>
                                    <th>Sale Returns Amount</th>
                                    <th>Received Amount</th>
                                    <th>Order Due Amount</th>
                                    <th>Reseller Amount</th>
                                    <th>Reseller Profit Amount</th>
                                    <th>Reseller Paid Amount</th>
                                    <th>Reseller Due Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $val)
                                    <tr>
                                        <td><a
                                                href="{{ route('admin.report.reseller-transactions') . '?reseller=' . $val->id }}">{{ $val->code }}</a>
                                        </td>
                                        <td>{{ $val->name }}</td>
                                        <td>{{ $val->address }}</td>
                                        <td>{{ $val->mobile }}</td>
                                        <td style="text-align:right">{{ $val->orderQuantity }}</td>
                                        <td style="text-align:right">{{ $val->saleQuantity }}</td>
                                        <td style="text-align:right">{{ number_format($val->SaleAmount, 2) }}</td>
                                        <td style="text-align:right">{{ $val->saleReturnQuantity }}</td>
                                        <td style="text-align:right">{{ number_format($val->saleReturnAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->receivedAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->orderDueAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->resellerAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->resellerProfitAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->resellerPaymentAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->resellerDueAmount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4">Total</th>
                                    <th style="text-align: right;">{{$reports->sum('orderQuantity') }}</th>
                                    <th style="text-align: right;">{{$reports->sum('saleQuantity') }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('SaleAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{$reports->sum('saleReturnQuantity') }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('saleReturnAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('receivedAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('orderDueAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('resellerAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('resellerProfitAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('resellerPaymentAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('resellerDueAmount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
