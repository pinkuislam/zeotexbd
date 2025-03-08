@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Customer Ledger
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.report.customer-transactions') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Customer Ledger Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.customer') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.report.customer') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Mobile</th>
                                    <th>Opening Due</th>
                                    <th>Sales</th>
                                    <th>Discount</th>
                                    <th>Shipping Charge</th>
                                    <th>Return</th>
                                    <th>Received</th>
                                    <th>Paid</th>
                                    <th>Adjust</th>
                                    <th style="text-align: right;">Due Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $key => $val)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><a href="{{ route('admin.report.customer-transactions') . '?customer=' . $val->id }}">{{ $val->name }}</a></td>
                                        <td width="10%">{{ $val->address }}</td>
                                        <td>{{ $val->mobile }}</td>
                                        <td style="text-align:right">{{ number_format($val->opening_due, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->saleAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->discountAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->TotalShippingAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->returnAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->receivedAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->paidAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->adjustmentAmount, 2) }}</td>
                                        <td style="text-align: right;">{{ $val->dueAmount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4">Total</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('opening_due'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('saleAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('discountAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('TotalShippingAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('returnAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('receivedAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('paidAmount'), 2) }}</th>
                                    <th style="text-align: right;"> {{ number_format($reports->sum('adjustmentAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('dueAmount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 pagi-msg">{!! pagiMsg($reports) !!}</div>

                        <div class="col-sm-4 text-center">
                            {{ $reports->appends(Request::except('page'))->links() }}
                        </div>

                        <div class="col-sm-4">
                            <div class="pagi-limit-box">
                                <div class="input-group pagi-limit-box-body">
                                    <span class="input-group-addon">Show:</span>
                                    <select class="form-control pagi-limit" name="limit">
                                        @foreach (reportPaginations() as $pag)
                                            <option value="{{ qUrl(['limit' => $pag]) }}" {{ $pag == Request::get('limit') ? 'selected' : '' }}>{{ $pag }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
