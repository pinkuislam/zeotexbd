@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Dyeing Agent Ledger
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.report.dyeing-agent-transactions') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Dyeing Agent Ledger Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.dyeing-agent') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.report.dyeing-agent') }}">X</a>
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
                                    <th>Mobile</th>{{-- 
                                    <th>Grey Send</th>
                                    <th>Grey Received</th>
                                    <th>Grey In Hand</th> --}}
                                    <th>Total Cost</th>
                                    <th>Received</th>
                                    <th>Paid</th>
                                    <th>Adjust</th>
                                    <th style="text-align: right;">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $val)
                                    <tr>
                                        <td><a
                                                href="{{ route('admin.report.dyeing-agent-transactions') . '?dyeing_agent=' . $val->id }}">{{ $val->code }}</a>
                                        </td>
                                        <td>{{ $val->name }}</td>
                                        <td>{{ $val->contact_no }}</td>{{-- 
                                        <td style="text-align:right">{{ $val->send }}</td>
                                        <td style="text-align:right">{{ $val->received }}</td>
                                        <td style="text-align:right">{{ $val->in_hand }}</td> --}}
                                        <td style="text-align:right">{{ number_format($val->total_cost, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->receivedAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->paidAmount, 2) }}</td>
                                        <td style="text-align:right">{{ number_format($val->adjustmentAmount, 2) }}</td>
                                        <td style="text-align: right;">{{ $val->dueAmount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>{{-- 
                                    <th style="text-align: right;">{{ $reports->sum('send') }}</th>
                                    <th style="text-align: right;">{{ $reports->sum('received') }}</th>
                                    <th style="text-align: right;">{{ $reports->sum('in_hand') }}</th> --}}
                                    <th style="text-align: right;">{{ number_format($reports->sum('total_cost'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('receivedAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('paidAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('adjustmentAmount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($reports->sum('dueAmount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
