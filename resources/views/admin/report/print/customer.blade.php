@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">Name</th>
            <th scope="col" style="text-align: left;font-size: 12px">Address</th>
            <th scope="col" style="text-align: left;font-size: 12px">Mobile</th>
            <th scope="col" style="text-align: left;font-size: 12px">Sales</th>
            <th scope="col" style="text-align: left;font-size: 12px">Discount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Shipping Charge</th>
            <th scope="col" style="text-align: left;font-size: 12px">Return</th>
            <th scope="col" style="text-align: left;font-size: 12px">Received</th>
            <th scope="col" style="text-align: left;font-size: 12px">Paid</th>
            <th scope="col" style="text-align: left;font-size: 12px">Adjust</th>
            <th scope="col" style="text-align: left;font-size: 12px">Due Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $key => $val)
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->name }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->address }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->mobile }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->saleAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->discountAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->TotalShippingAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->returnAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->receivedAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->paidAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->adjustmentAmount, 2) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->dueAmount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px" colspan="3">Total</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('opening_due'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('saleAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('discountAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('returnAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('receivedAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('paidAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px"> {{ number_format($reports->sum('adjustmentAmount'), 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('dueAmount'), 2) }}</th>
        </tr>
    </tfoot>
</table>
@endsection