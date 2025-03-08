@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">Code</th>
            <th scope="col" style="text-align: left;font-size: 12px">Bank Name</th>
            <th scope="col" style="text-align: left;font-size: 12px">Branch Name</th>
            <th scope="col" style="text-align: left;font-size: 12px">Account Name</th>
            <th scope="col" style="text-align: left;font-size: 12px">Account Number</th>
            <th scope="col" style="text-align: left;font-size: 12px">Opening Balance</th>
            <th scope="col" style="text-align: left;font-size: 12px">Received Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Payment Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $val)
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->code }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->bank_name }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->branch_name }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->account_name}}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->account_no}}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->opening_balance }} {{ env('CURRENCY') }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->inAmount }} {{ env('CURRENCY') }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->outAmount }} {{ env('CURRENCY') }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->balanceAmount }} {{ env('CURRENCY') }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px" colspan="5">Total</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('opening_balance')) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('inAmount')) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('outAmount')) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('balanceAmount')) }}</th>
        </tr>
    </tfoot>
</table>
@endsection