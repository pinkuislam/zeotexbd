@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">SL</th>
            <th scope="col" style="text-align: left;font-size: 12px">Date</th>
            <th scope="col" style="text-align: left;font-size: 12px">Type</th>
            <th scope="col" style="text-align: left;font-size: 12px">Purchase</th>
            <th scope="col" style="text-align: left;font-size: 12px">Consume</th>
            <th scope="col" style="text-align: left;font-size: 12px">Stock</th>
            <th scope="col" style="text-align: left;font-size: 12px">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stockQty = $stockPrice = $totalPurchaseQty = $totalPurchaseAmt = $totalUsedQty = $totalUsedAmt = 0;
        ?>
        @foreach ($reports as $key=>$val)
            @if ($val->type == 'Consume')
                @php($stockQty -= $val->quantity)
                @php($stockPrice -= $accessory->unit_price * $val->quantity)
                @php($totalUsedQty += $val->quantity)
                @php($totalUsedAmt += $accessory->unit_price * $val->quantity)
            @elseif($val->type == 'Purchase')
                @php($stockQty += $val->quantity)
                @php($stockPrice += $accessory->unit_price * $val->quantity)
                @php($totalPurchaseQty += $val->quantity)
                @php($totalPurchaseAmt += $accessory->unit_price * $val->quantity)
            @endif
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $key + 1 }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ dateFormat($val->created_at) }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->type }}</td>

                <td scope="col" style="text-align: left;font-size: 12px">
                    @if ($val->type == 'Purchase')
                        {{ $val->quantity }}
                    @else
                        0
                    @endif
                </td>
                <td scope="col" style="text-align: left;font-size: 12px">
                    @if ($val->type == 'Consume')
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
            <th colspan="6"></th>
            <th colspan="3">
                <table class="table">
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Total Purchase Qty : </th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ $totalPurchaseQty }}
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" style="text-align: left;font-size: 12px">Total Consume Qty : </th>
                        <th scope="col" style="text-align: left;font-size: 12px">
                            {{ $totalUsedQty }}</th>
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