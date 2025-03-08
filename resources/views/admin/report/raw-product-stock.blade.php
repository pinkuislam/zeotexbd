@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="{{ route('admin.report.product-stock', 'raw-material') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Fabric Stock Reports
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.report.product-ledger') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Fabric Stock Details Reports
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.product-stock', $type) }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat"
                                        href="{{ route('admin.report.product-stock', $type) }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Product</th>
                                    <th style="text-align: right;">Stock In (Purchase/From Dyeing)</th>
                                    <th style="text-align: right;">To Dyeing</th>
                                    <th style="text-align: right;">Used</th>
                                    <th style="text-align: right;">Return</th>
                                    <th style="text-align: right;">Damage</th>
                                    <th style="text-align: right;">Stock</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $val)
                                    <tr>
                                        <td><a href="{{ route('admin.report.product-ledger') . '?product_id=' . $val->id }}">{{ $val->product_code }}</a></td>
                                        <td>{{ $val->product_name }}</td>
                                        <td style="text-align: right;">
                                            {{ $val->purchase_quantity ?? 0}} ({{ $val->purchase_quantity /1000}} Kg)
                                        </td>
                                        <td style="text-align: right;">
                                            {{ $val->dyeing_quantity ?? 0}} ({{ $val->dyeing_quantity /1000}} Kg)
                                        </td>
                                        <td style="text-align: right;">
                                            {{ $val->production_quantity ?? 0}} ({{ $val->production_quantity /1000}} Kg)
                                        </td>
                                        <td style="text-align: right;">
                                            {{ $val->purchase_return_quantity ?? 0}} ({{ $val->purchase_return_quantity /1000}} Kg)
                                        </td>
                                        <td style="text-align: right;">
                                            {{ $val->damage_quantity ?? 0}} ({{ $val->damage_quantity /1000}} Kg)
                                        </td>
                                        <td style="text-align: right;">
                                            {{ $val->stock_quantity ?? 0}} ({{ $val->stock_quantity /1000}} Kg)
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($val->stock_amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th style="text-align:right" colspan="2">Total</th>
                                    <th style="text-align:right">{{ $reports->sum('purchase_quantity')}}
                                    </th>
                                    <th style="text-align:right">{{ $reports->sum('dyeing_quantity')}}</th>
                                    <th style="text-align:right">{{ $reports->sum('production_quantity')}}</th>
                                    <th style="text-align:right">{{ $reports->sum('purchase_return_quantity')}}</th>
                                    <th style="text-align:right">{{ $reports->sum('damage_quantity')}}</th>
                                    <th style="text-align:right">{{ $reports->sum('stock_quantity')}}</th>
                                    <th style="text-align:right">{{ number_format($reports->sum('stock_amount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
