@extends('layouts.print')
@section('content')

<div class="box-body table-responsive">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <table class="table table-bordered border-dark">
                <tr>
                    <th scope="col" style="text-align: left;font-size: 12px">Name</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->bank_name }}</td>
                    <th scope="col" style="text-align: left;font-size: 12px">Branch</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->branch_name }}</td>
                    <th scope="col" style="text-align: left;font-size: 12px">Account Name</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->account_name }}</td>
                    <th scope="col" style="text-align: left;font-size: 12px">Account No.</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->account_no }}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <table class="table table-bordered border-dark">
        <thead>
            <tr>
                <th scope="col" style="text-align: left;font-size: 12px">Sl</th>
                <th scope="col" style="text-align: left;font-size: 12px">Date</th>
                <th scope="col" style="text-align: left;font-size: 12px">Vch Type</th>
                <th scope="col" style="text-align: left;font-size: 12px">Type</th>
                <th scope="col" style="text-align: left;font-size: 12px">Note</th>
                <th scope="col" style="text-align: left;font-size: 12px">Received Amount</th>
                <th scope="col" style="text-align: left;font-size: 12px">Payment Amount</th>
                <th scope="col" style="text-align: left;font-size: 12px">Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $withdraw  = $deposit =  0;
            ?>
            @php($sl = $balance = 0)
            @if ($openingBalance > 0)
                <tr>
                    <th scope="col" style="text-align: left;font-size: 12px" colspan="6" style="text-align:center">Opening Balance</th>
                    <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($openingBalance) }}</th>
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
                    <td scope="col" style="text-align: left;font-size: 12px">{{ ++$sl }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ dateFormat($val->datetime, 1) }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $val->flag }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $val->type }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $val->note }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">
                        @if ($val->type == 'Received')
                            <?php $deposit += $val->amount; ?>
                            {{ $val->amount }}
                        @endif
                        
                    </td>
                    <td scope="col" style="text-align: left;font-size: 12px">
                        @if ($val->type == 'Payment')
                            <?php $withdraw += $val->amount; ?>
                            {{ $val->amount }}
                        @endif
                    </td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $balance }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th scope="col" style="text-align: left;font-size: 12px" colspan="5">Total</th>
                <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($deposit, 2) }}</th>
                <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($withdraw, 2) }}
                </th>
                <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($balance, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>

@endsection