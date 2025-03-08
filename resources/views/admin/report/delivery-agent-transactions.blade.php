@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.report.delivery-agent') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Delivery Agent Ledger
                    </a>
                </li>
                <li class="active">
                    <a href="">
                        <i class="fa fa-list" aria-hidden="true"></i> Delivery Agent Ledger Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.delivery-agent-transactions') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select class="form-control select2" name="delivery_agent">
                                        <option value="">Any Delivery Agent</option>
                                        @foreach ($delivery_agents as $delivery_agent)
                                            <option value="{{ $delivery_agent->id }}"
                                                {{ Request::get('delivery_agent') == $delivery_agent->id ? 'selected' : '' }}>
                                                {{ $delivery_agent->name }}</option>
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
                                    <a class="btn btn-warning btn-flat"
                                        href="{{ route('admin.report.delivery-agent-transactions') }}">X</a>
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
                                            <th>Type</th>
                                            <td>{{ $data->type }}</td>
                                            <th>Number</th>
                                            <td>{{ $data->mobile }}</td>
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
                                        <th style="text-align: right;">Shipping Charge</th>
                                        <th style="text-align: right;">Received</th>
                                        <th style="text-align: right;">Paid</th>
                                        <th style="text-align: right;">Adjustment</th>
                                        <th style="text-align: right;">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($balance = 0)
                                    @if ($openingBalance->balance)
                                        @php($balance += $openingBalance->balance)
                                        <tr>
                                            <th colspan="9" class="text-center">Opening Balance</th>
                                            <th style="text-align: right;">
                                                {{ number_format($openingBalance->balance, 2) }}
                                            </th>
                                        </tr>
                                    @endif

                                    @php($sl = 1)
                                    <?php
                                    $sales = $return = $received = $paid = $adjustment = 0;
                                    ?>
                                    @foreach ($reports as $val)
                                        @if ($val->type == 'shippingChargeAmount' || $val->type == 'Received')
                                            @php($balance += $val->amount)
                                        @else
                                            @php($balance -= $val->amount)
                                        @endif
                                        <tr>
                                            <td>{{ $sl++ }}</td>
                                            <td><a href="{{ route($val->route, $val->id) }}"
                                                    target="_blank">{{ $val->code }}</a></td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->type }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'shippingChargeAmount')
                                                    <?php $sales += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'Received')
                                                    <?php $received += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'Payment')
                                                    <?php $paid += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'Adjustment')
                                                    <?php $adjustment += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <th style="text-align: right;">{{ number_format($balance, 2) }}</th>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align:right">Total</th>
                                        <th style="text-align: right">{{ number_format($sales, 2) }}</th>
                                        <th style="text-align: right">{{ number_format($received, 2) }}</th>
                                        <th style="text-align: right">{{ number_format($paid, 2) }}</th>
                                        <th style="text-align: right">{{ number_format($adjustment, 2) }}</th>
                                        <th style="text-align: right"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <h2 class="text-center" style="padding: 50px 0;">Select Delivery Agent first!</h2>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
