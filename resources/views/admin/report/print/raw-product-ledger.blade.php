@extends('layouts.print')
@section('content')
<h2 class="text-center" style="margin:0; text-decoration: underline;">{{ $product->name }}</h2>
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">SL</th>
            <th scope="col" style="text-align: left;font-size: 12px">Date</th>
            <th scope="col" style="text-align: left;font-size: 12px">Type</th>
            <th scope="col" style="text-align: left;font-size: 12px">Purchase</th>
            <th scope="col" style="text-align: left;font-size: 12px">From Dyeing</th>
            <th scope="col" style="text-align: left;font-size: 12px">Used In Production</th>
            <th scope="col" style="text-align: left;font-size: 12px">To Dyeing</th>
            <th scope="col" style="text-align: left;font-size: 12px">Damage</th>
            <th scope="col" style="text-align: left;font-size: 12px">Return</th>
            <th scope="col" style="text-align: left;font-size: 12px">Stock</th>
            <th scope="col" style="text-align: left;font-size: 12px">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stockQty = $stockPrice = $totalPurchaseQty = $totalPurchaseAmt = $totalUsedQty = $totalUsedAmt = 0;
        ?>
        @foreach ($reports as $key=>$val)
            @if ($val->type == 'PurchaseReturn' || $val->type == 'Damage' || $val->type == 'Production' || $val->type == 'ToDyeing')
                @php($stockQty -= $val->quantity)
                @php($stockPrice -= $product->stock_price * $val->quantity)
                @php($totalUsedQty += $val->quantity)
                @php($totalUsedAmt += $product->stock_price * $val->quantity)
            @elseif($val->type == 'Purchase' || $val->type == 'FromDyeing')
                @php($stockQty += $val->quantity)
                @php($stockPrice += $product->stock_price * $val->quantity)
                @php($totalPurchaseQty += $val->quantity)
                @php($totalPurchaseAmt += $product->stock_price * $val->quantity)
            @endif
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $key + 1 }}</td>
                <td scope="col" style="text-align: left;font-size: 12px"> {{ dateFormat($val->created_at) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->type }}</td>

                <td scope="col" style="text-align: left;font-size: 12px">
                    @if ($val->type == 'Purchase')
                        {{ $val->quantity }}
                    @else
                        0
                    @endif
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    @if ($val->type == 'FromDyeing')
                        {{ $val->quantity }}
                    @else
                        0
                    @endif
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    @if ($val->type == 'Production')
                        {{ $val->quantity }}
                    @else
                        0
                    @endif
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    @if ($val->type == 'ToDyeing')
                        {{ $val->quantity }}
                    @else
                        0
                    @endif
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    @if ($val->type == 'Damage')
                        {{ $val->quantity }}
                    @else
                        0
                    @endif
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    @if ($val->type == 'PurchaseReturn')
                        {{ $val->quantity }}
                    @else
                        0
                    @endif
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ $stockQty }}
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    {{ number_format($stockPrice, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px" colspan="6"></th>
            <th scope="col" style="text-align: left;font-size: 12px" colspan="5">
                <table class="table">
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Total Purchase Qty : </th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ $totalPurchaseQty }}
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Total Purchase Amount : </th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ $totalPurchaseAmt }}</th>
                    </tr>
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Total Used Qty : </th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ $totalUsedQty }}</th>
                    </tr>
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Total Used Amount : </th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ $totalUsedAmt }}
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Total Stock Qty : </th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ $stockQty }}</th>
                    </tr>
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Total Stock Amount : </th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ number_format($stockPrice, 2) }}
                        </th>
                    </tr>
                </table>
            </th>
        </tr>
    </tfoot>
</table>
@endsection