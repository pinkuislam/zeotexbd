@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">Code</th>
            <th scope="col" style="text-align: left;font-size: 12px">Name</th>
            <th scope="col" style="text-align: left;font-size: 12px">Purchase Quantity</th>
            <th scope="col" style="text-align: left;font-size: 12px">Used Quantity</th>
            <th scope="col" style="text-align: left;font-size: 12px">Stock Quantity</th>
            <th scope="col" style="text-align: left;font-size: 12px">Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $totalquantity = 0;
            $totalPrice = 0;
        ?>
        @foreach ($reports as $val)
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $val->code }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->name }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->totalQty }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->totalUsedQty }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->stockQty }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ ($val->stockQty * $val->unit_price) }}</td>
            </tr>
            <?php
                $totalquantity += $val->stockQty;
                $totalPrice += ($val->stockQty * $val->unit_price);
            ?>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td scope="col" style="text-align: left;font-size: 12px" colspan="4">Total</td>
            <td scope="col" style="text-align: left;font-size: 12px">{{ $totalquantity }}</td>
            <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($totalPrice, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endsection