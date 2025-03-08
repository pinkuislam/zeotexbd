@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.report.bank') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Bank Ledger
                    </a>
                </li>
                <li class="active">
                    <a >
                        <i class="fa fa-list" aria-hidden="true"></i> Bank Ledger Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.bank-transactions') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select name="bank" class="form-control" required>
                                        <option value="">Select Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ Request::get('bank') == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->bank_name }}</option>
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
                                        href="{{ route('admin.report.bank-transactions') }}">X</a>
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
                                            <th>Name</th>
                                            <td>{{ $data->bank_name }}</td>
                                            <th>Branch</th>
                                            <td>{{ $data->branch_name }}</td>
                                            <th>Account Name</th>
                                            <td>{{ $data->account_name }}</td>
                                            <th>Account No.</th>
                                            <td>{{ $data->account_no }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Date</th>
                                        <th>Vch Type</th>
                                        <th>Type</th>
                                        <th>Note</th>
                                        <th style="text-align: right;">Received Amount</th>
                                        <th style="text-align: right;">Payment Amount</th>
                                        <th style="text-align: right;">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $withdraw  = $deposit =  0;
                                    ?>
                                    @php($sl = $balance = 0)
                                    @if ($openingBalance > 0)
                                        <tr>
                                            <th colspan="6" style="text-align:center">Opening Balance</th>
                                            <th style="text-align:right">{{ number_format($openingBalance) }}</th>
                                            @php($balance += $openingBalance)
                                        </tr>
                                    @endif
                                    @foreach ($reports as $val)
                                        @if ($val->type == 'Payment')
                                            @php($balance -= $val->amount)
                                        @else
                                            @php($balance += $val->amount)
                                        @endif
                                        <tr>
                                            <td>{{ ++$sl }}</td>
                                            <td>{{ dateFormat($val->datetime, 1) }}</td>
                                            <td>{{ $val->flag }}</td>
                                            <td>{{ $val->type }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'Received')
                                                    <?php $deposit += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                                
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'Payment')
                                                    <?php $withdraw += $val->amount; ?>
                                                    {{ $val->amount }}
                                                @endif
                                            </td>
                                            <td style="text-align: right;">{{ $balance }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align:right">Total</th>
                                        <th style="text-align: right">{{ number_format($deposit, 2) }}</th>
                                        <th style="text-align: right">{{ number_format($withdraw, 2) }}
                                        </th>
                                        <th style="text-align: right">{{ number_format($balance, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <h2 class="text-center" style="padding: 50px 0;">Select a bank first!</h2>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
