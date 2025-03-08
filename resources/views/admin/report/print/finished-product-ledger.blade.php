@extends('layouts.print')
@section('content')
<h2 class="text-center" style="margin:0; text-decoration: underline;">{{ $product->name }}</h2>
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">SL</th>
            <th scope="col" style="text-align: left;font-size: 12px">Date</th>
            <th scope="col" style="text-align: left;font-size: 12px">Type</th>
            <th scope="col" style="text-align: left;font-size: 12px">Quantity</th>
            <th scope="col" style="text-align: left;font-size: 12px">Stock</th>
            <th scope="col" style="text-align: left;font-size: 12px">Stock Amount</th>
        </tr>
    </thead>
    <tbody>
        @php($stockQty = 0)
        @php($stockAmount = 0)
        @foreach ($reports as $key=>$val)
            @if ($val->type == 'Damage' || $val->type == 'Sale' || $val->type == 'Purchase Return')
                @php($stockQty -= $val->quantity)
                @php($stockAmount -= ($val->quantity * $product->unit_price))
            @else
                @php($stockQty += $val->quantity)
                @php($stockAmount += ($val->quantity * $product->unit_price))
            @endif
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $key + 1 }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ dateFormat($val->created_at) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->type }}</td>

                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->quantity }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $stockQty }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ number_format($stockAmount, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px" colspan="4">Total</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($stockQty, 2) }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($stockAmount, 2) }}</th>
        </tr>
    </tfoot>
</table>
@endsection