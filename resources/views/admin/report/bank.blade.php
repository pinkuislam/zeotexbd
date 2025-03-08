@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Bank Ledger
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.report.bank-transactions') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Bank Ledger Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.bank') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.report.bank') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Bank Name</th>
                                    <th>Branch Name</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th style="text-align: right;">Opening Balance</th>
                                    <th style="text-align: right;">Received Amount</th>
                                    <th style="text-align: right;">Payment Amount</th>
                                    <th style="text-align: right;">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $val)
                                    <tr>
                                        <td><a
                                                href="{{ route('admin.report.bank-transactions') . '?bank=' . $val->id }}">{{ $val->code }}</a>
                                        </td>
                                        <td>{{ $val->bank_name }}</td>
                                        <td>{{ $val->branch_name }}</td>
                                        <td>{{ $val->account_name}}</td>
                                        <td>{{ $val->account_no}}</td>
                                        <td style="text-align: right;">{{ $val->opening_balance }} {{ env('CURRENCY') }}
                                        </td>
                                        <td style="text-align: right;">{{ $val->inAmount }} {{ env('CURRENCY') }}</td>
                                        <td style="text-align: right;">{{ $val->outAmount }} {{ env('CURRENCY') }}</td>
                                        <td style="text-align: right;">{{ $val->balanceAmount }} {{ env('CURRENCY') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align:right">Total</th>
                                    <th style="text-align:right">{{ number_format($reports->sum('opening_balance')) }}</th>
                                    <th style="text-align:right">{{ number_format($reports->sum('inAmount')) }}</th>
                                    <th style="text-align:right">{{ number_format($reports->sum('outAmount')) }}</th>
                                    <th style="text-align:right">{{ number_format($reports->sum('balanceAmount')) }}</th>
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
                                        @foreach (paginations() as $pag)
                                            <option value="{{ qUrl(['limit' => $pag]) }}"
                                                {{ $pag == Request::get('limit') ? 'selected' : '' }}>
                                                {{ $pag }}</option>
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
