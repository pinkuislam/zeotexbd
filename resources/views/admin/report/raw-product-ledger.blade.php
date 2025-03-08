@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.report.product-stock', 'raw-material') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Fabric Stock Reports
                    </a>
                </li>
                <li class="active">
                    <a href="{{ route('admin.report.product-ledger') . '?product_id=' . Request::get('product_id').'&color_id='. Request::get('color_id') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Fabric Stock Details Reports
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.product-ledger') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select name="product_id" class="form-control select2" required>
                                        <option value="">Select Product</option>
                                        @foreach ($products as $pro)
                                            <option value="{{ $pro->id }}"
                                                {{ Request::get('product_id') == $pro->id ? 'selected' : '' }}>
                                                {{ $pro->name }}</option>
                                        @endforeach
                                    </select>
                                </div>{{-- 
                                <div class="form-group">
                                    <select name="color_id" class="form-control select2" required>
                                        <option value="">Select Color</option>
                                        @foreach ($colors as $color)
                                            <option value="{{ $color->id }}"
                                                {{ Request::get('color_id') == $color->id ? 'selected' : '' }}>
                                                {{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                <div class="form-group">
                                    <input type="text" class="form-control" name="from" id="datepickerFrom"
                                        value="{{ Request::get('from') }}" placeholder="From Date">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="to" id="datepickerTo"
                                        value="{{ Request::get('to') }}" placeholder="To Date">
                                </div>
                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat"
                                        href="{{ route('admin.report.product-ledger') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (isset($product))
                        <div class="box-body table-responsive">
                            <h2 class="text-center" style="margin:0; text-decoration: underline;">{{ $product->name }}</h2>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th style="text-align: right;">Purchase</th>
                                        <th style="text-align: right;">From Dyeing</th>
                                        <th style="text-align: right;">Used In Production</th>
                                        <th style="text-align: right;">To Dyeing</th>
                                        <th style="text-align: right;">Damage</th>
                                        <th style="text-align: right;">Return</th>
                                        <th style="text-align: right;">Stock</th>
                                        <th style="text-align: right;">Amount</th>
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
                                            <td>{{ $key + 1 }}</td>
                                            <td> <a href="{{ route($val->route, $val->rowId) }}"
                                                target="_blank">{{ dateFormat($val->created_at) }}</a></td>
                                            <td>{{ $val->type }}</td>

                                            <td style="text-align: right;">
                                                @if ($val->type == 'Purchase')
                                                    {{ $val->quantity }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'FromDyeing')
                                                    {{ $val->quantity }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'Production')
                                                    {{ $val->quantity }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'ToDyeing')
                                                    {{ $val->quantity }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'Damage')
                                                    {{ $val->quantity }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                @if ($val->type == 'PurchaseReturn')
                                                    {{ $val->quantity }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                {{ $stockQty }}
                                            </td>
                                            <td style="text-align: right;">
                                                {{ number_format($stockPrice, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="8"></th>
                                        <th colspan="3">
                                            <table class="table">
                                                <tr>
                                                    <th>Total Purchase Qty : </th>
                                                    <th style="text-align:right">
                                                        {{ $totalPurchaseQty }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Total Purchase Amount : </th>
                                                    <th style="text-align:right">
                                                        {{ $totalPurchaseAmt }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Total Used Qty : </th>
                                                    <th style="text-align:right">
                                                        {{ $totalUsedQty }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Total Used Amount : </th>
                                                    <th style="text-align:right">
                                                        {{ $totalUsedAmt }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Total Stock Qty : </th>
                                                    <th style="text-align:right">
                                                        {{ $stockQty }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Total Stock Amount : </th>
                                                    <th style="text-align:right">
                                                        {{ number_format($stockPrice, 2) }}
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <h2 class="text-center" style="padding: 50px 0;">Select a product first!</h2>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
