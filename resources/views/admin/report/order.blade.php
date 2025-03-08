@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Orders Ledger
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.orders') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select class="form-control select2" name="customer_id">
                                        <option value="">Any Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ Request::get('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name . '-'. $customer->mobile }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control " name="status">
                                        <option value="">Any Status</option>
                                        @foreach (['Ordered','Processing','Delivered'] as $status)
                                            <option value="{{ $status }}"
                                                {{ Request::get('status') == $status ? 'selected' : '' }}>
                                                {{ $status }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control " name="type">
                                        <option value="">Any Type</option>
                                        @foreach (['Seller','Reseller'] as $type)
                                            <option value="{{ $type }}"
                                                {{ Request::get('type') == $type ? 'selected' : '' }}>
                                                {{ $type }} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="from" id="datepickerFrom"
                                        value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="to" id="datepickerTo"
                                        value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="q" value="" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.report.orders') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Admin/Seller/Reseller</th>
                                    <th>Customer/Reseller Business</th>
                                    <th>Delivery Agent</th>
                                    <th>Date</th>
                                    <th>Collage</th>
                                    <th>Items</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Discount Amount</th>
                                    <th>Shipping Charage</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Due Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $total_paid_amount = 0;
                                ?>
                                @foreach ($result as $val)
                                    <tr>
                                        <td>{{ $val->code }}</td>
                                        <td>{{ optional($val->user)->name }}</td>
                                        <td>
                                            @if($val->customer)
                                            {{ $val->customer->name }} - {{ $val->customer->mobile }}
                                            @else
                                            {{$val->resellerBusiness ? $val->resellerBusiness->name . '-'. $val->resellerBusiness->mobile  : '' }}
                                            @endif
                                        </td>
                                        <td>{{ optional($val->delivery)->name }}</td>
                                        <td>{{ dateFormat($val->date) }}</td>
                                        <td>
                                            @if ($val->images)
                                                @foreach ($val->images as $item)
                                                {!! viewImg('orders', $item->image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($val->items as $key => $item)
                                                {{  optional($item->product)->name }}
                                                <span
                                                    class="label label-default">{{ number_format($item->quantity, 0) }} {{ optional($item->unit)->name }} {{ optional($item->color)->name }}</span>
                                                @if (($key + 1) % 3 == 0)
                                                <br>
                                                @else
                                                @if (!$loop->last)
                                                ,
                                                @endif
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>{{ isset($val->createdBy) ? $val->createdBy->name : '' }}</td>
                                        <td>
                                        {{ $val->status }}
                                        </td>
                                        <td>
                                            {{ number_format($val->discount_amount, 2) }}
                                        </td>
                                        <td>
                                            {{ number_format($val->shipping_charge, 2) }}
                                        </td>
                                        <td>
                                            {{ number_format($val->amount, 2) }}
                                        </td>
                                        <td>
                                          <?php
                                                if ($val->sale) {
                                                    $paid_amount = ($val->customerPayment + $val->sale->saleConfirmPayment);
                                                }else {
                                                    $paid_amount = $val->customerPayment;
                                                }
                                                $total_paid_amount += $paid_amount;
                                            ?>
                                            {{ number_format($paid_amount, 2) }}
                                        </td>
                                        <td>
                                            {{ number_format( (float)$val->amount - (float)($paid_amount) , 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="9">Total</th>
                                    <th style="text-align: right;">{{ number_format($result->sum('discount_amount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($result->sum('shipping_charge'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($result->sum('amount'), 2) }}</th>
                                    <th style="text-align: right;">{{ number_format($total_paid_amount, 2) }}</th>
                                    <th style="text-align: right;">{{ number_format( $result->sum('amount') - $total_paid_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 pagi-msg">{!! pagiMsg($result) !!}</div>

                        <div class="col-sm-4 text-center">
                            {{ $result->appends(Request::except('page'))->links() }}
                        </div>

                        <div class="col-sm-4">
                            <div class="pagi-limit-box">
                                <div class="input-group pagi-limit-box-body">
                                    <span class="input-group-addon">Show:</span>
                                    <select class="form-control pagi-limit" name="limit">
                                        @foreach (reportPaginations() as $pag)
                                            <option value="{{ qUrl(['limit' => $pag]) }}" {{ $pag == Request::get('limit') ? 'selected' : '' }}>{{ $pag }}</option>
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
