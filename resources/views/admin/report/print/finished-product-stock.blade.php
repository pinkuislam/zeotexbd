@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">SL</th>
            <th scope="col" style="text-align: left;font-size: 12px">Code</th>
            <th scope="col" style="text-align: left;font-size: 12px">Product</th>
            <th scope="col" style="text-align: left;font-size: 12px">Color</th>
            <th scope="col" style="text-align: left;font-size: 12px">Production Qty</th>
            <th scope="col" style="text-align: left;font-size: 12px">Purchase</th>
            <th scope="col" style="text-align: left;font-size: 12px">Sale Return</th>
            <th scope="col" style="text-align: left;font-size: 12px">Sale</th>
            <th scope="col" style="text-align: left;font-size: 12px">Damage</th>
            <th scope="col" style="text-align: left;font-size: 12px">Purchase Return</th>
            <th scope="col" style="text-align: left;font-size: 12px">Stock</th>
            <th scope="col" style="text-align: left;font-size: 12px">Stock Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $key=>$val)
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $key+1}}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->product_code }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->product_name }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->color_name }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->production_quantity }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->purchase_quantity }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->sale_return_quantity }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->sale_quantity }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->damage_quantity }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->purchase_return_quantity }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->stock_quantity }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ number_format(($val->stock_amount),2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px" colspan="4">Total Qty</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ $reports->sum('production_quantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ $reports->sum('purchase_quantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ $reports->sum('sale_return_quantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ $reports->sum('sale_quantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ $reports->sum('damage_quantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ $reports->sum('purchase_return_quantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ $reports->sum('stock_quantity') }}</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{  number_format( $reports->sum('stock_amount') , 2) }}</th>
        </tr>
    </tfoot>
</table>
@endsection