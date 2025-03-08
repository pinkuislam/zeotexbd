@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">Code</th>
            <th scope="col" style="text-align: left;font-size: 12px">Name</th>
            <th scope="col" style="text-align: left;font-size: 12px">Address</th>
            <th scope="col" style="text-align: left;font-size: 12px">Mobile</th>
            <th scope="col" style="text-align: left;font-size: 12px">Orders</th>
            <th scope="col" style="text-align: left;font-size: 12px">Sales Comfirm</th>
            <th scope="col" style="text-align: left;font-size: 12px">Sales Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Sale Returns</th>
            <th scope="col" style="text-align: left;font-size: 12px">Sale Returns Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Received Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Order Due Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Reseller Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Reseller Profit Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Reseller Paid Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Reseller Due Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $val)
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->code }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->name }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->address }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->mobile }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->orderQuantity }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->saleQuantity }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->SaleAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->saleReturnQuantity }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->saleReturnAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->receivedAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->orderDueAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->resellerAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->resellerProfitAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->resellerPaymentAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->resellerDueAmount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px" colspan="4">Total</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{$reports->sum('orderQuantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{$reports->sum('saleQuantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('SaleAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{$reports->sum('saleReturnQuantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('saleReturnAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('receivedAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('orderDueAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('resellerAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('resellerProfitAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('resellerPaymentAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('resellerDueAmount'), 2) }}</th>
        </tr>
    </tfoot>
</table>
@endsection