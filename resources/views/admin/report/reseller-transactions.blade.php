@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.report.reseller') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Reseller Ledger
                    </a>
                </li>
                <li class="active">
                    <a href="">
                        <i class="fa fa-list" aria-hidden="true"></i> Reseller Ledger Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.reseller-transactions') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select class="form-control select2" name="reseller">
                                        <option value="">Any Reseller</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ Request::get('reseller') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}</option>
                                        @endforeach
                                    </select>
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
                                    <a class="btn btn-warning btn-flat"
                                        href="{{ route('admin.report.reseller-transactions') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (isset($data))
                        <div class="box-body table-responsive">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <th>Code</th>
                                            <td>{{ $data->code }}</td>
                                            <th>Name</th>
                                            <td>{{ $data->name }}</td>
                                            <th>Email</th>
                                            <td>{{ $data->email }}</td>
                                            <th>Number</th>
                                            <td>{{ $data->mobile }}</td>
                                            <th>Address</th>
                                            <td>{{ $data->address }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>Voucher Type</th>
                                        <th>Note</th>
                                        <th>Order Amount</th>
                                        <th>Sale Amount</th>
                                        <th>Sale Return Amount</th>
                                        <th>Received Amount</th>
                                        <th>Reseller Paid Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $orders = 0;
                                        $sales = 0;
                                        $saleReturns = 0;
                                        $received = 0;
                                        $paid = 0;
                                    @endphp
                                    @foreach ($reports as $key=>$val)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td><a href="{{ route($val->route, $val->id) }}"
                                                    target="_blank">{{ $val->code }}</a></td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->type }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td style="text-align: center;">
                                                @if ($val->type == 'Orders')
                                                <?php $orders += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if ($val->type == 'Sales')
                                                <?php $sales += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if ($val->type == 'Sale Returns')
                                                <?php $saleReturns += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if ($val->type == 'Received')
                                                    <?php $received += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if ($val->type == 'Payment')
                                                    <?php $paid += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align:center">Total</th>
                                        <th style="text-align: center">{{ number_format($orders, 2) }}</th>
                                        <th style="text-align: center">{{ number_format($sales, 2) }}</th>
                                        <th style="text-align: center">{{ number_format($saleReturns, 2) }}</th>
                                        <th style="text-align: center">{{ number_format($received, 2) }}</th>
                                        <th style="text-align: center">{{ number_format($paid, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <h2 class="text-center" style="padding: 50px 0;">Select Reseller first!</h2>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
