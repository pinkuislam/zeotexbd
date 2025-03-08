@extends('layouts.app')
@push('styles')
<style>
    .show_image{
        height: 80px !important;
        width: 80px !important;
    }
</style>    
@endpush
@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.sale.sales.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Sale List
                    </a>
                </li>

                @can('add sale')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.sale.sales.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Sale
                        </a>
                    </li>
                @endcan

                @can('edit sale')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Sale
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show sale')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Sale Detail
                            </a>
                        </li>
                    @endif
                @endcan
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div style="text-align: right">
                            @can('edit sale')
                                @if ($data->status != 'Canceled')
                                    <a href="{{route('admin.sale.sales.cancel',$data->id)}}" class="btn btn-danger">Cancel Sale</a>
                                @endif
                                @if ($data->status != 'Delivered' && $data->status != 'Canceled')
                                    <button data-toggle="modal" data-target="#deliveryModal" class="btn btn-info">Confirm Delivery</button>
                                @endif
                            @endcan
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Code</th>
                                    <th>:</th>
                                    <td>{{ $data->code }}</td>
                                </tr>
                                @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                    <tr>
                                        <th style="width:120px;">Type</th>
                                        <th style="width:10px;">:</th>
                                        <td>{{ $data->type }}</td>
                                    </tr>
                                    @if ($data->type == 'Seller' || $data->type == 'Reseller')    
                                    <tr>
                                        <th style="width:120px;">{{ $data->type }}</th>
                                        <th style="width:10px;">:</th>
                                        <td>{{ optional($data->user)->name }}</td>
                                    </tr>
                                    @endif
                                @endif
                                <tr>
                                    <th>Customer/Reseller Business </th>
                                    <th>:</th>
                                    <td>{{ $data->customer != null ? optional($data->customer)->name .'-'. optional($data->customer)->mobile : optional($data->resellerBusiness)->name  .'-'. optional($data->resellerBusiness)->mobile }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Shipping Method</th>
                                    <th>:</th>
                                    <td>{{ $data->shipping != null ? $data->shipping->name : '' }}</td>
                                </tr> --}}
                                <tr>
                                    <th>Delivery Agent</th>
                                    <th>:</th>
                                    <td>{{ $data->delivery != null ? $data->delivery->name : '' }}</td>
                                </tr>

                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->date) }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{{ $data->note ?? '' }}</td>
                                </tr>

                                <tr>
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>
                                      {{ $data->status }}
                                    </td>
                                </tr>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>Color</th>
                                        <th style="text-align: right;">Quantity</th>
                                        <th style="text-align: right;">Unit Price</th>
                                        @if ($data->type == 'Reseller')
                                        <th style="text-align: right;">Reseller Unit Price</th>
                                        @endif
                                        <th style="text-align: right;">Total Price</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($data->items as $key => $val)
                                        <tr>
                                            <td>{{ $val->product ? $val->product->name : '-' }}</td>
                                            <td>{{ $val->unit ? $val->unit->name : '-' }}</td>
                                            <td>{{ $val->color ? $val->color->name : '-' }}</td>
                                            <td style="text-align: right;">{{ number_format($val->quantity, 2) }}</td>

                                            <td style="text-align: right;">{{ number_format($val->unit_price, 2) }}</td>
                                            @if ($data->type == 'Reseller')
                                            <td style="text-align: right;">{{ number_format($val->reseller_unit_price, 2) }}</td>
                                            @endif
                                            <td style="text-align: right;">{{ number_format($val->unit_price * $val->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;" colspan="3">Total Quantity :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->items->sum('quantity'), 2) }}
                                        </th>
                                        <th style="text-align: right;" colspan="{{$data->type == 'Reseller' ? 2 : 1}}">SubTotal Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->subtotal_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="{{$data->type == 'Reseller' ? 6 : 5}}">Vat Percent :</th>
                                        <th style="text-align: right;">
                                            {{ $data->vat_percent }} %
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="{{$data->type == 'Reseller' ? 6 : 5}}">Vat Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->vat_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="{{$data->type == 'Reseller' ? 6 : 5}}">Shipping Charge :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->shipping_charge, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="{{$data->type == 'Reseller' ? 6 : 5}}">Discount Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->discount_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="{{$data->type == 'Reseller' ? 6 : 5}}">Advance Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->advance_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="{{$data->type == 'Reseller' ? 6 : 5}}">Total Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->total_amount, 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @include('admin.sale.inc.delivery')
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            @if ($code)
                            <div class="row">
                                <div class="col-md-8">
                                    <form method="get" action="{{ route('admin.sale.getorder') }}{{ qString() }}" class="form-horizontal">
                                    @csrf
                                        <div class="form-group ">
                                            <label for=" " class="control-label col-sm-3">Order Code</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" name="code" id="search_order" placeholder="Order Code" required value="{{ $code }}">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="submit" class="btn btn-info btn-flat"> Search Order</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.sale.sales.update', $edit) : route('admin.sale.sales.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                @if (isset($edit))
                                    @method('PUT')
                                @endif
                                @if ($order)
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <div class="row">
                                    <div class="col-md-8">
                                        @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                            <div class="form-group">
                                                <label class="control-label col-sm-3 required">Type:</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="type" value="{{ $order->type }}" readonly>
                                                    @if ($errors->has('type'))
                                                    <span class="text-danger">{{ $errors->first('type') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($order->type == 'Seller' || $order->type == 'Reseller')
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3 required">Seller or Reseller:</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" value="{{ optional($order->user)->name }}" readonly>
                                                        <input type="hidden" name="user_id" value="{{ $order->user_id }}" >
                                                        @if ($errors->has('user_id'))
                                                            <span class="text-danger">{{ $errors->first('user_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        @if ($order->customer)
                                            
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Customer :</label>
                                            <div class="col-sm-9">
                                                    <input type="text" class="form-control" value="{{ $order->customer->name . '-'. $order->customer->mobile }}" readonly>
                                                    <input type="hidden" name="customer_id" value="{{ $order->customer_id }}" >
                                                @if ($errors->has('customer_id'))
                                                    <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                        @if ($order->resellerBusiness) 
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Reseller Business :</label>
                                            <div class="col-sm-9">
                                                    <input type="text" class="form-control" value="{{ $order->resellerBusiness->name }}" readonly>
                                                    <input type="hidden" name="reseller_business_id" value="{{ $order->reseller_business_id }}" >
                                                @if ($errors->has('reseller_business_id'))
                                                    <span class="text-danger">{{ $errors->first('reseller_business_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                        {{-- <div class="form-group">
                                            <label class="control-label col-sm-3 required">Shipping Method :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="shipping_rate_id" required id="shipping_rate_id">
                                                    <option value="">Select Shipping Method</option>
                                                    @php($shipping_rate_id = old('shipping_rate_id', isset($edit) ? $data->shipping_rate_id : $order->shipping_rate_id))
                                                    @foreach ($shipping_methods as $shipping_method)
                                                        <option value="{{ $shipping_method->id }}"
                                                            {{ $shipping_rate_id == $shipping_method->id ? 'selected' : '' }}>
                                                            {{ $shipping_method->name . '-' . $shipping_method->area . '-' .number_format($shipping_method->rate,2) }} </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('shipping_rate_id'))
                                                    <span class="text-danger">{{ $errors->first('shipping_rate_id') }}</span>
                                                @endif
                                            </div>
                                        </div> --}}
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Delivery Agent :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="delivery_agent_id" required id="delivery_agent_id">
                                                    <option value="">Select Delivery Agent</option>
                                                    @php($delivery_agent_id = old('delivery_agent_id', isset($edit) ? $data->delivery_agent_id : $order->delivery_agent_id))
                                                    @foreach ($delivery_agents as $delivery_agent)
                                                        <option value="{{ $delivery_agent->id }}"
                                                            {{ $delivery_agent_id == $delivery_agent->id ? 'selected' : '' }}>
                                                            {{ $delivery_agent->name }} </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('delivery_agent_id'))
                                                    <span class="text-danger">{{ $errors->first('delivery_agent_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}"
                                                    required>
                                                @if ($errors->has('date'))
                                                    <span class="text-danger">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Note :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="note"
                                                    value="{{ old('note', isset($data) ? $data->note : '') }}">

                                                @if ($errors->has('note'))
                                                    <span class="text-danger">{{ $errors->first('note') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-body table-responsive" id="sale_table">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th class="required">Product</th>
                                                <th >Unit</th>
                                                <th >Color</th>
                                                <th >Order Quantity</th>
                                                <th class="required">Quantity</th>
                                                <th class="required">Unit Price</th>
                                                <th class="required">Total Price</th>
                                            </tr>
                                        </thead>
                                        <?php 
                                        $subTotal = 0;
                                        ?>
                                        <tbody id="responseHtml">
                                            @foreach ( isset($edit) ? $data->items : $order->items as $key => $item)
                                                <tr class="subRow" id="row{{ $key }}">
                                                    <td>
                                                        {{ $key + 1 }}
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $item->product->name }}" readonly>
                                                        <input type="hidden" name="product_id[]" value="{{ $item->product_id }}" id="product_id{{ $key }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $item->unit ? $item->unit->name : '' }}" readonly>
                                                        <input type="hidden" name="unit_id[]" value="{{ $item->unit_id }}" id="unit_id{{ $key }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $item->color ? $item->color->name: '' }}" readonly>
                                                        <input type="hidden" name="color_id[]" value="{{ $item->color_id }}" id="color_id{{ $key }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="order_quantity[]" value="{{ $item->quantity }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control qty" name="quantity[]"
                                                            id="quantity{{ $key }}"
                                                            value="{{ $item->quantity }}" required readonly>
                                                    </td>

                                                    
                                                    <td>
                                                        <input type="number" class="form-control"
                                                        name="unit_price[]" id="unit_price{{ $key }}"
                                                        value="{{ $item->unit_price }}" readonly>
                                                    </td>
                                                    <td>

                                                        <input type="number" class="form-control total_price"
                                                            name="amount[]" id="amount{{ $key }}"
                                                            value="{{ $item->quantity * $item->unit_price }}" readonly>
                                                    </td>
                                                </tr>
                                                <?php 
                                                   $subTotal += $item->quantity * $item->unit_price;
                                                ?>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-right" colspan="5"><strong>Total Quantity
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_quantity" id="total_quantity"
                                                        value="{{ isset($edit) ? $data->items->sum('quantity') : $order->items->sum('quantity') }}">
                                                </td>
                                                <td class="text-right"><strong> Sub Total Amount
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="sub_total_amount" id="sub_total_amount"
                                                        value="{{ numberFormat($subTotal) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7"><strong> Shipping Charge
                                                    :</strong>
                                            </td>
                                            <td class="text-right"><input type="text" class="form-control" readonly
                                                     name="shipping_charge" id="shipping_charge"
                                                    value="{{ isset($edit) ? numberFormat($data->shipping_charge) : $order->shipping_charge  }}">
                                            </td>
                                            </tr>
                                            <tr>
                                                <tr>
                                                    <td class="text-right" colspan="7">
                                                        <strong>Vat({{ isset($data) ? $data->vat_percent : env('VAT_PERCENT') }}%)
                                                            :</strong>
                                                    </td>
                                                    <td class="text-right"><input type="number" class="form-control"
                                                            name="vat_percent" onkeyup="totalCal()" onclick="totalCal()" id="vat_percent"
                                                            value="{{ old('vat_percent', isset($data) ? $data->vat_percent : env('VAT_PERCENT')) }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right" colspan="7">
                                                        <strong>Vat Amount :</strong>
                                                    </td>
                                                    <td class="text-right"><input type="text" class="form-control"
                                                            name="vat_amount" id="vat_amount" readonly
                                                            value="{{ old('vat_amount', isset($data) ? $data->vat_amount : '') }}">
                                                    </td>
                                                </tr>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7">
                                                    <strong>Advance Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        name="advance_amount" id="advance_amount" readonly
                                                        value="{{ old('advance_amount', isset($data) ? $data->advance_amount : $order->advance_amount) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7">
                                                    <strong>Discount Amount :</strong>
                                                </td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control" onkeyup="totalCal()" onclick="totalCal()" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', isset($data) ? $data->discount_amount : numberFormat($order->discount_amount)) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7"><strong> Total Amount
                                                    :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_amount" id="total_amount"
                                                        value="{{ isset($edit) ? numberFormat($data->total_amount) : numberFormat($order->amount)  }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7"><strong> Remainning Due:</strong></td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control"
                                                     readonly id="remainning_due"
                                                    value="{{ isset($edit) ? numberFormat($data->total_amount - $order->customerPayment) : numberFormat($order->amount - $order->customerPayment) }}">
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="form-group">
                                    <div class="text-center">
                                        <button type="submit"
                                            class="btn btn-success btn-flat">{{ isset($edit) ? __('Update') : __('Submit') }}</button>
                                        <button type="reset"
                                            class="btn btn-warning btn-flat">{{ __('Clear') }}</button>
                                    </div>
                                </div>
                                @endif
                            </form>
                            @else
                            <div class="row">
                                <div class="col-md-8">
                                    <form method="get" action="{{ route('admin.sale.getorder') }}{{ qString() }}" class="form-horizontal">
                                    @csrf
                                        <div class="form-group ">
                                            <label for=" " class="control-label col-sm-3"> Order Code</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" name="code" id="search_order" placeholder="Order Code" required value="{{ $code }}">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="submit" class="btn btn-info btn-flat"> Search Order</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                            
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.sale.sales.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select class="form-control select2" name="customer_id">
                                            <option value="">Any Customer</option>
                                            @foreach ($customers as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ Request::get('customer_id') == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name . '-'. $val->mobile }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control " name="status">
                                            <option value="">Any Status</option>
                                            @foreach (['Processing','Delivered','Canceled'] as $status)
                                                <option value="{{ $status }}"
                                                    {{ Request::get('status') == $status ? 'selected' : '' }}>
                                                    {{ $status }} </option>
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
                                        <button type="submit"
                                            class="btn btn-info btn-flat">{{ __('Search') }}</button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.sale.sales.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Date</th>
                                        <th>Admin/Seller/Reseller</th>
                                        <th>Customer</th>
                                        <th>Address</th>
                                        <th>Items</th>
                                        <th>Advance Amount</th>
                                        <th>Total Amount</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ optional($val->user)->name }}</td>
                                            <td>
                                                @if ($val->customer)
                                                    {{ $val->customer->name}} -  {{$val->customer->mobile }}
                                                @else
                                                    {{ $val->resellerBusiness ? $val->resellerBusiness->name . '-'. $val->resellerBusiness->mobile : '' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($val->customer)
                                                    {{ $val->customer->address}}
                                                @else
                                                    {{ $val->resellerBusiness ? $val->resellerBusiness->address : '' }}
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($val->items as $key => $item)
                                                    {{ $item->product->name ?? '-' }}
                                                    <span
                                                        class="label label-default">{{ number_format($item->quantity, 0) }} {{ $item->unit ? $item->unit->name: '' }} {{ $item->color ? $item->color->name: '' }}</span>
                                                    @if (($key + 1) % 3 == 0)
                                                    <br>
                                                    @else
                                                    @if ($loop->last)
                                                    @else
                                                    ,
                                                    @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ number_format($val->advance_amount, 2) }}</td>
                                            <td>{{ number_format($val->total_amount, 2) }} </td>
                                            <td>{{ isset($val->createdBy) ? $val->createdBy->name : '' }}</td>
                                            <td>
                                               {{ $val->status }}
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show sale')
                                                        <li><a href="{{ route('admin.sale.sales.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                                        @endif
                                                        @if ($val->status != 'Canceled')
                                                        @can('invoice sale')
                                                            <li><a href="{{ route('admin.sale.sales.invoice', $val->id) . qString() }}" target="_blank"><i class="fa fa-file-o"></i> Invoice</a></li>
                                                        @endif
                                                        @endcan
                                                        @can('edit sale')
                                                        @if ($val->status != 'Canceled')
                                                            <li><a href="{{ route('admin.sale.sales.edit', $val->id) . qString() }}"><i class="fa fa-pencil"></i> Edit</a></li>
                                                        @endif
                                                        @endcan
                                                        @can('delete sale')
                                                            <li><a onclick="deleted('{{ route('admin.sale.sales.destroy', $val->id) . qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
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
                                            @foreach (paginations() as $pag)
                                                <option value="{{ qUrl(['limit' => $pag]) }}" {{ $pag == Request::get('limit') ? 'selected' : '' }}>{{ $pag }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function chkItemPrice(key) {
            var quantity = Number($('#quantity' + key).val());
            var unit_price = Number($('#unit_price' + key).val());
            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Please Provide Valid Quantity!');
            }
            if (quantity > 0 && unit_price > 0)
            var total = Number(quantity * unit_price);
            $('#amount' + key).val(Number(total).toFixed(2));
            totalCal();
        }

        function totalCal() {
            var quantity = 0;
            $("input[id^='quantity']").each(function() {
                quantity += +$(this).val();
            });
            $('#total_quantity').val(quantity);

            var subTotal = 0;
            $("input[id^='amount']").each(function() {
                subTotal += Number($(this).val());
            });
            $('#sub_total_amount').val(Number(subTotal).toFixed(2));
            var shipping_charge = Number($('#shipping_charge').val());
            var discount_amount = Number($('#discount_amount').val());
            var advance_amount = Number($('#advance_amount').val());
            var taxAmount = 0;
            var taxPercent = $('#vat_percent').val();
            if (taxPercent > 0) {
                taxAmount = ((subTotal * taxPercent) / 100);
            }
            $('#vat_amount').val(Number(taxAmount).toFixed(2));
            var total = ((subTotal + taxAmount + shipping_charge - discount_amount));
            var remainning_due =  Number(total - advance_amount);
            $('#total_amount').val(Number(total).toFixed(2));
            $('#remainning_due').val(Number(remainning_due).toFixed(2));
        }
    //     $('#shipping_rate_id').on('change', function(){
    //             let id = $(this).val();
    //             getShippingCharge(id);
    //     });
    //     function getShippingCharge(key) {
    //     $.ajax({
    //         url: '{{ route('admin.basic.shippingcharge') }}',
    //         type: "GET",
    //         dataType: 'json',
    //         data: {
    //             id: key,
    //         },
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         success: function(response) {
    //             $('#shipping_charge').val(response.data.rate);
    //             totalCal();
    //         }
    //     })
    // }
    function checkStock(key) {
        var quantity = Number($('#quantity' + key).val());
        var stock = Number($('#stock' + key).val());
        if (stock < quantity) {
            $('#quantity' + key).val('');
            $('#quantity' + key).focus();
            alerts('Stock quantity not exist!');
        }
        chkItemPrice(key);
    }
    </script>
@endpush