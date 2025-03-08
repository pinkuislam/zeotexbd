@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.ecommerce.orders.index') . qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Order List
                </a>
            </li>

            <li class="active">
                <a href="javascript:void(0);">
                    <i class="fa fa-eye" aria-hidden="true"></i> Order Details
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Order Details</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width:140px;">Order ID</th>
                                        <th style="width:10px;">:</th>
                                        <td>#{{ $data->serial_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Name </th>
                                        <th>:</th>
                                        <td>{{ $data->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Phone </th>
                                        <th>:</th>
                                        <td>{{ $data->phone ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Address </th>
                                        <th>:</th>
                                        <td>{{ $data->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Image </th>
                                        <th>:</th>
                                        <td>
                                            @if ($data->ecommerceOrderImages)
                                            @foreach ($data->ecommerceOrderImages as $item)
                                            {!! viewImg('orders', $item->image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}
                                            @endforeach
                                        @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Order Status </th>
                                        <th>:</th>
                                        <td>{{ $data->status ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <th>:</th>
                                        <td>{{ dateFormat($data->created_at) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Amount</th>
                                        <th>:</th>
                                        <td>{{ config('settings.currency') }}{{ $data->total_amount }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <h4>Order Items</h4>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Barcode</th>
                                    <th>Product</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-right">Quantity</th>
                                    <th class="text-right">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->ecommerceOrders as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) }}</td>
                                    <td>{{ $item->barcode }}</td>
                                    <td>
                                        {{ $item->product->name }}<br />
                                        @if ($item->color)
                                            Color: {{ $item->color->name }},
                                        @endif

                                        @if ($item->size)
                                            Size: {{ $item->size->name }}
                                        @endif
                                    </td>
                                    <td class="text-right">{{ config('settings.currency') }}{{ $item->sale_price }}</td>
                                    <td class="text-right">{{ $item->quantity }}</td>
                                    <td class="text-right">{{ config('settings.currency') }}{{ $item->amount }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Sub Total</th>
                                    <th class="text-right">{{ config('settings.currency') }}{{ $data->sub_total_amount }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5">Shipping Charge</th>
                                    <th class="text-right">{{ config('settings.currency') }}{{ optional($data->shipping)->rate }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5">Total Price</th>
                                    <th class="text-right">{{ config('settings.currency') }}{{ $data->total_amount }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
