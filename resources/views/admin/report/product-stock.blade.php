@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> {{ __('Product Stock') }} {{ __('Reports') }}
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
                                    <button type="submit" class="btn btn-info btn-flat">{{ __('Search') }}</button>
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
                                    <th>Product</th>
                                    @if ($type != 'raw-material')
                                        <th style="text-align: right;">Production Qty</th>
                                        <th style="text-align: right;">Sale</th>
                                        <th style="text-align: right;">Sale Return</th>
                                    @endif
                                    <th style="text-align: right;">Purchase</th>
                                    <th style="text-align: right;">Return</th>
                                    <th style="text-align: right;">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $val)
                                    <tr>
                                        <td><a
                                                href="{{ route('admin.report.others-product-ledger') . '?product=' . $val->id }}">{{ $val->name }}</a>
                                        </td>
                                        @if ($type != 'raw-material')
                                            <td style="text-align: right;">
                                                {{ number_format($val->productionQty, 2) }}
                                            </td>
                                            <td style="text-align: right;">
                                                {{ number_format($val->saleQty, 2) }}
                                            </td>
                                            <td style="text-align: right;">
                                                {{ number_format($val->saleReturnQty, 2) }}</td>
                                        @endif
                                        <td style="text-align: right;">
                                            {{ number_format($val->purchaseQty, 2) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($val->returnQty, 2) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($val->stockQty, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 pagi-msg">{!! pagiMsg($reports) !!}</div>

                        <div class="col-sm-4 text-center">
                            {{ $reports->appends(Request::except('page'))->links() }}
                        </div>

                        <div class="col-sm-4">
                            <div class="pagi-limit-box">
                                <div class="input-group pagi-limit-box-body">
                                    <span class="input-group-addon">Show:</span>

                                    <select class="form-control pagi-limit" name="limit">
                                        @foreach (paginations() as $pag)
                                            <option value="{{ qUrl(['limit' => $pag]) }}"
                                                {{ $pag == Request::get('limit') ? 'selected' : '' }}>
                                                {{ $pag }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
