@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <div class="box-body table-responsive">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <table class="table table-bordered border-dark">
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Code</th>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ $data->code }}</td>
                        <th scope="col" style="text-align: left;font-size: 12px">Name</th>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ $data->name }}</td>
                        <th scope="col" style="text-align: left;font-size: 12px">Number</th>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ $data->mobile }}</td>
                        <th scope="col" style="text-align: left;font-size: 12px">Address</th>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ $data->address }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <hr>
        <table class="table table-bordered border-dark">
            <thead>
                <tr>
                    <th scope="col" style="text-align: left;font-size: 12px">Sl</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Invoice No.</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Date</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Voucher Type</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Note</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Sales</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Return</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Received</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Paid</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Adjustment</th>
                    <th scope="col" style="text-align: left;font-size: 12px">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php($balance = 0)
                @if ($openingBalance->balance)
                    @php($balance += $openingBalance->balance)
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px" colspan="10" class="text-center">Opening Balance</th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ number_format($openingBalance->balance, 2) }}
                        </th>
                    </tr>
                @endif

                @php($sl = 1)
                <?php
                $sales = $return = $received = $paid = $adjustment = 0;
                ?>
                @foreach ($reports as $val)
                    @if ($val->type == 'Sale' || $val->type == 'Payment')
                        @php($balance += $val->amount)
                        @php($balance -= (int) $val->discount)
                    @else
                        @php($balance -= $val->amount)
                    @endif
                    <tr>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ $sl++ }}</td>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ $val->code }}</td>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ dateFormat($val->date) }}</td>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ $val->type }}</td>
                        <td scope="col" style="text-align: left;font-size: 12px">{{ $val->note }}</td>
                        <td scope="col" style="text-align: left;font-size: 12px">
                            @if ($val->type == 'Sale')
                                <?php $sales += $val->amount; ?>
                                {{ $val->amount }}
                            @endif
                        </td>
                        <td scope="col" style="text-align: left;font-size: 12px">
                            @if ($val->type == 'Sale Return')
                                <?php $return += $val->amount; ?>
                                {{ $val->amount }}
                            @endif
                        </td>
                        <td scope="col" style="text-align: left;font-size: 12px">
                            @if ($val->type == 'Received')
                                <?php $received += $val->amount; ?>
                                {{ $val->amount }}
                            @endif
                        </td>
                        <td scope="col" style="text-align: left;font-size: 12px">
                            @if ($val->type == 'Payment')
                                <?php $paid += $val->amount; ?>
                                {{ $val->amount }}
                            @endif
                        </td>
                        <td scope="col" style="text-align: left;font-size: 12px">
                            @if ($val->type == 'Adjustment')
                                <?php $adjustment += $val->amount; ?>
                                {{ $val->amount }}
                            @endif
                        </td>
                        <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($balance, 2) }}</th>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col" style="text-align: left;font-size: 12px" colspan="5">Total</th>
                    <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($sales, 2) }}</th>
                    <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($return, 2) }}</th>
                    <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($received, 2) }}</th>
                    <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($paid, 2) }}</th>
                    <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($adjustment, 2) }}</th>
                    <th scope="col" style="text-align: left;font-size: 12px"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</table>
@endsection