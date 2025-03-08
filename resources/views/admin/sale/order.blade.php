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
                    <a href="{{ route('admin.orders.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Sale Order List
                    </a>
                </li>

                @can('add orders')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.orders.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Sale Order
                        </a>
                    </li>
                @endcan

                @can('edit orders')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Sale Order
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show orders')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Sale Order Detail
                            </a>
                        </li>
                    @endif
                @endcan
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
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
                                    <th>Customer/Reseller Business</th>
                                    <th>:</th>
                                    <td>
                                        @if($data->customer)
                                        {{ $data->customer->name }} - {{ $data->customer->mobile }} 
                                        @else
                                        {{$data->resellerBusiness ? $data->resellerBusiness->name . '-'. $data->resellerBusiness->mobile  : '' }}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->date) }}</td>
                                </tr>
                                <tr>
                                    <th>Collage</th>
                                    <th>:</th>
                                    <td>
                                        @if ($data->images)
                                            @foreach ($data->images as $item)
                                            {!! viewImg('orders', $item->image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{{ $data->note ?? '' }}</td>
                                </tr>
                                {{-- <tr>
                                    <th>Shipping</th>
                                    <th>:</th>
                                    <td>{{ optional($data->shipping)->name }}</td>
                                </tr> --}}
                                <tr>
                                    <th>Delivery Agent</th>
                                    <th>:</th>
                                    <td>{{ optional($data->delivery)->name }}</td>
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
                                        <th style="text-align: right;">Total Price</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($data->items as $key => $val)
                                        <tr>
                                            <td>{{ $val->product ? $val->product->name : '-' }}</td>
                                            <td>{{ $val->unit ? $val->unit->name : '-' }}
                                            </td>
                                            <td>{{ $val->color ? $val->color->name : '-' }}</td>
                                            <td style="text-align: right;">{{ number_format($val->quantity, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->unit_price, 2) }}</td>
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
                                        <th style="text-align: right;">Total Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->items->sum('amount'), 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="5">Shipping Charge :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->shipping_charge, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="5">Discount Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->discount_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="5">Advance Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->advance_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="5">Total Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->amount, 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.orders.update', $edit) : route('admin.orders.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                            <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-3 required">Type:</label>
                                                <div class="col-sm-9">
                                                    <select name="type" class="form-control select2" required id="type">
                                                        @php($type = old('type', isset($data) ? $data->type : ''))
                                                        @foreach (['Admin', 'Seller', 'Reseller', 'Reseller Business'] as $sts)
                                                            <option value="{{ $sts }}"
                                                                {{ $type == $sts ? 'selected' : '' }}>{{ $sts }}
                                                            </option>
                                                        @endforeach
                                                    </select>
        
                                                    @if ($errors->has('type'))
                                                        <span class="help-block">{{ $errors->first('type') }}</span>
                                                    @endif
                                                </div>
                                            </div>    
                                            <div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }}" id="user" @if (isset($data)) @if (!($data->type == "Seller" || $data->type == "Reseller")) style="display: none" @endif   @else style="display: none" @endif>
                                                <label class="control-label col-sm-3">Seller or Reseller:</label>
                                                <div class="col-sm-9">
                                                    <select name="user_id" class="form-control select2" id="user_id">
                                                        <option value=""> Select Seller or Reseller</option>
                                                        @if (isset($data))
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}" {{ $user->id == $data->user_id ? "selected" : ""  }}> {{ $user->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
        
                                                    @if ($errors->has('user_id'))
                                                        <span class="help-block">{{ $errors->first('user_id') }}</span>
                                                    @endif
                                                </div>
                                            </div>    
                                        @endif
                                        <div class="form-group" id="reseller_business" @if (isset($data)) @if ($data->type == "Reseller Business") style="display: block" @endif  style="display: none"  @else style="display: none" @endif>
                                            <label class="control-label col-sm-3 required">Reseller Business :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="reseller_business_id" required id="reseller_business_id">
                                                    <option value="">Select Reseller Business</option>
                                                    @php($reseller_business_id = old('reseller_business_id', isset($data) ? $data->reseller_business_id : ''))
                                                    @if (isset($data))
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ $reseller_business_id == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} </option>
                                                    @endforeach
                                                    @endif
                                                </select>

                                                @if ($errors->has('reseller_business_id'))
                                                    <span class="help-block">{{ $errors->first('reseller_business_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group" id="customer" @if (isset($data)) @if ($data->type == "Reseller Business") style="display: none" @endif @endif>
                                            <label class="control-label col-sm-3 required">Customer :</label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <select class="form-control select2" name="customer_id" required id="customer_id">
                                                        <option value="">Select Customer</option>
                                                        @php($customer_id = old('customer_id', isset($data) ? $data->customer_id : ''))
                                                        @foreach ($customers as $customer)
                                                            <option value="{{ $customer->id }}"
                                                                {{ $customer_id == $customer->id ? 'selected' : '' }}>
                                                                {{ $customer->name }} - {{ $customer->mobile }} </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="input-group-addon" data-toggle="modal" data-target="#customerModal">
                                                        <i class="fa fa-plus"></i>
                                                    </span>
                                                    @if ($errors->has('customer_id'))
                                                        <span class="help-block">{{ $errors->first('customer_id') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="form-group">
                                            <label class="control-label col-sm-3 required">Shipping Method :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="shipping_rate_id" required id="shipping_rate_id">
                                                    <option value="">Select Shipping Method</option>
                                                    @php($shipping_rate_id = old('shipping_rate_id', isset($data) ? $data->shipping_rate_id : ''))
                                                    @foreach ($shipping_methods as $shipping_method)
                                                        <option value="{{ $shipping_method->id }}"
                                                            {{ $shipping_rate_id == $shipping_method->id ? 'selected' : '' }}>
                                                            {{ $shipping_method->name . '-' . $shipping_method->area . '-' .number_format($shipping_method->rate,2) }} </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('shipping_rate_id'))
                                                    <span class="help-block">{{ $errors->first('shipping_rate_id') }}</span>
                                                @endif
                                            </div>
                                        </div> --}}
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Delivery Agent :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="delivery_agent_id" required id="delivery_agent_id">
                                                    <option value="">Select Delivery Agent</option>
                                                    @php($delivery_agent_id = old('delivery_agent_id', isset($data) ? $data->delivery_agent_id : ''))
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
                                                    value="{{ old('date', isset($data) ? dbDateFormat($data->date) : date('d-m-Y')) }}"
                                                    required>

                                                @if ($errors->has('date'))
                                                    <span class="help-block">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Collage :</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" id="image" name="image[]" multiple>
                                                @if (isset($data))
                                                    @if ($data->images)
                                                        @foreach ($data->images as $item)
                                                        {!! viewImg('orders', $item->image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}
                                                        @endforeach
                                                    @endif
                                                @endif
                                                @if ($errors->has('image'))
                                                    <span class="help-block">{{ $errors->first('image') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Note :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="note"
                                                    value="{{ old('note', isset($data) ? $data->note : '') }}">

                                                @if ($errors->has('note'))
                                                    <span class="help-block">{{ $errors->first('note') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div>
                                            <h5> <strong>Customer Name:</strong> <span id="customer_name"></span></h5>
                                            <h5> <strong>Mobile Number:</strong> <span id="customer_mobile_number"></span></h5>
                                            <h5> <strong>Customer Address:</strong> <span id="customer_address"></span></h5>
                                            <h5> <strong>Customer Due:</strong> <span id="customer_due"></span></h5>
                                            <h5> <strong>Customer Orders:</strong> <span id="customer_orders"></span></h5>
                                        </div>
                                        {{-- <div>
                                            <a target="_blank" href="{{route('admin.user.customer.history',$customers[0]->id)}}" id="customerHistory" disabled class="btn btn-info btn-flat" style="margin-top: 50px">Customer History</a>
                                        </div> --}}
                                    </div>
                                </div>

                                <div class="box-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="required">Product</th>
                                                <th >Unit</th>
                                                <th >Color</th>
                                                <th class="required">Quantity</th>
                                                <th class="required">Unit Price</th>
                                                <th class="required">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="responseHtml">
                                            @foreach ($items as $key => $item)
                                                <tr class="subRow" id="row{{ $key }}">
                                                    <td>
                                                        @if ($key == 0)
                                                            <a class="btn btn-success btn-flat"
                                                                onclick="addRow({{ $key }})"><i
                                                                    class="fa fa-plus"></i></a>
                                                        @else
                                                            <a class="btn btn-danger btn-flat"
                                                                onclick="removeRow({{ $key }})"><i
                                                                    class="fa fa-minus"></i></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <select name="product_id[]" id="product_id{{ $key }}"
                                                            class="form-control select2"
                                                            onchange="checkProduct({{ $key }})" required>
                                                            <option value="">Select Product</option>
                                                            @foreach ($products as $product)
                                                                <option data-unit_id="{{ $product->unit->id }}" data-unit_name="{{ $product->unit->name }}" 
                                                                    data-unit_price="{{ $product->product_type == 'Combo' ? $product->getSalePrice() : $product->sale_price }}"
                                                                    value="{{ $product->id }}"
                                                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                    {{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text"  id="unit_id{{ $key }}" class="form-control" readonly value="{{  $item->unit_id ?  $item->unit->name : ''}}">
                                                        <input type="hidden" name="unit_id[]"  id="unit{{ $key }}" class="form-control" value="{{  $item->unit_id ?  $item->unit->id : ''}}">
                                                    </td>
                                                    <td>
                                                        <select name="color_id[]" id="color_id{{ $key }}"
                                                            class="form-control select2" onchange="checkProduct({{ $key }})">
                                                            <option value="">Select Color</option>
                                                            @foreach ($colors as $color)
                                                                <option value="{{ $color->id }}"
                                                                    {{ $item->color_id == $color->id ? 'selected' : '' }}>
                                                                    {{ $color->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control qty" name="quantity[]"
                                                            id="quantity{{ $key }}"
                                                            value="{{ $item->quantity }}"
                                                            onclick="chkItemPrice({{ $key }})"
                                                            onkeyup="chkItemPrice({{ $key }})" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="unit_price[]" id="unit_price{{ $key }}"
                                                            value="{{ $item->unit_price }}"
                                                            onkeyup="chkItemPrice({{ $key }})" onclick="chkItemPrice({{ $key }})">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control total_price"
                                                            name="amount[]" id="amount{{ $key }}"
                                                            value="{{ $item->amount }}" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td class="text-right" colspan="4"><strong>Total Quantity
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_quantity" id="total_quantity"
                                                        value="{{ isset($edit) ? $items->sum('quantity') : '' }}">
                                                </td>
                                                <td class="text-right"><strong> Sub Total Amount
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="sub_total_amount" id="sub_total_amount"
                                                        value="{{ isset($edit) ? numberFormat($items->sum('amount')) : '' }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong> Shipping Charge
                                                    :</strong>
                                            </td>
                                            <td class="text-right"><input type="text" class="form-control"
                                                    name="shipping_charge" id="shipping_charge" onclick="totalCal()" onkeyup="totalCal()"
                                                    value="{{ isset($edit) ? numberFormat($data->shipping_charge) : 0  }}">
                                            </td>
                                            </tr>
                                                <tr>
                                                    <td class="text-right" colspan="5"><strong> Advance Amount
                                                        :</strong>
                                                </td>
                                                <td>
                                                    @php($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
                                                    <select class="form-control" name="bank_id">
                                                        <option value=""> Select Bank </option>
                                                        @foreach ($banks as $bnk)
                                                            <option {{$bank_id == $bnk->id ? 'selected' : ''}}  value="{{ $bnk->id }}">{{ $bnk->bank_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        name="advance_amount" onclick="totalCal()" onkeyup="totalCal()" id="advance_amount" value="{{ isset($edit) ? numberFormat($data->advance_amount) : 0  }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong> Discount Amount:</strong></td>
                                                <td class="text-right"><input type="text" class="form-control" onclick="totalCal()" onkeyup="totalCal()"
                                                        name="discount_amount" id="discount_amount" value="{{ isset($edit) ? numberFormat($data->discount_amount) : 0  }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong> Total Amount:</strong></td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control"
                                                     readonly name="total_amount" id="total_amount"
                                                    value="{{ isset($edit) ? numberFormat($data->amount) : 0 }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong> Remainning Due:</strong></td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control"
                                                     readonly id="remainning_due"
                                                    value="{{ isset($edit) ? numberFormat($data->amount - $data->customerPayment) : 0 }}">
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
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.orders.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select class="form-control select2" name="customer_id">
                                            <option value="0">Any Customer</option>
                                            @foreach ($customers as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ Request::get('customer_id') == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name . '-'. $val->mobile }} </option>
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
                                        <button type="submit"
                                            class="btn btn-info btn-flat">{{ __('Search') }}</button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.orders.index') }}">X</a>
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
                                        <th>Customer/Reseller Business</th>
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
                                                @if($val->customer)
                                                    {{ $val->customer->name }} - {{ $val->customer->mobile }}
                                                @else
                                                    {{$val->resellerBusiness ? $val->resellerBusiness->name . '-'. $val->resellerBusiness->mobile  : '' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($val->customer)
                                                    {{ $val->customer->address }}
                                                @else
                                                    {{$val->resellerBusiness ? $val->resellerBusiness->address : '' }}
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
                                            <td>
                                                {{ number_format($val->advance_amount, 2) }}
                                            </td>
                                            <td>
                                                {{ number_format($val->amount, 2) }}
                                            </td>
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
                                                        @can('show orders')
                                                            <li><a
                                                                    href="{{ route('admin.orders.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                        @can('edit orders')
                                                            <li><a
                                                                    href="{{ route('admin.orders.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan

                                                        @can('delete orders')
                                                            <li><a
                                                                    onclick="deleted('{{ route('admin.orders.destroy', $val->id) . qString() }}')"><i
                                                                        class="fa fa-close"></i> Delete</a></li>
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
    @include('admin.sale.inc.customer')
@endsection

@push('scripts')
    <script>
        function checkProduct(key) {
            var product_id = $('#product_id' + key).val();
            var unit = $('#unit' + key).val();
            var color_id = $('#color_id' + key).val();
            var rowId = $(".subRow").length;

            var productOptions = $('#product_id' + key).html();
            var colorOptions = $('#color_id' + key).html();
    
             $('#unit_id' + key).val($('#product_id' + key).find(':selected').data('unit_name'));
             $('#unit' + key).val($('#product_id' + key).find(':selected').data('unit_id'));
            $('#unit_price' + key).val($('#product_id' + key).find(':selected').data('unit_price'));

            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#product_id' + x).val() == product_id && $('#unit' + x).val() == unit  && $('#color_id' + x).val() == color_id) {
                        $('#product_id' + key).html(productOptions);
                        $('#unit_id' + key).val('');
                        $('#color_id' + key).html(colorOptions);
                        alerts('This Product Already Entered In This Purchase.');
                        return false;
                    }
                }
            }
        }

        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var productOptions = $('#product_id' + key).html();
            var colorOptions = $('#color_id' + key).html();
            var unitOptions = $('#unit_id' + key).val();
            var unit_id = $('#unit' + key).val();
            var quantity = $('#quantity' + key).val();

            var html = `<tr class="subRow" id="row` + newKey + `">
                <td><a class="btn btn-danger btn-flat" onclick="removeRow(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
                <td>
                    <select name="product_id[]" id="product_id` + newKey +
                `" class="form-control select2" required onchange="checkProduct(` + newKey + `)">` + productOptions +
                `</select>
                </td>
                <td>
                    <input type="text"  id="unit_id` + newKey + `" class="form-control" readonly value="` + unitOptions + `">
                    <input type="hidden" name="unit_id[]"  id="unit` + newKey + `" class="form-control" value="` + unit_id + `">
                </td>
                <td>
                    <select name="color_id[]" id="color_id` + newKey +
                `" class="form-control select2" onchange="checkProduct(` + newKey + `)">` + colorOptions +
                `</select>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control qty" name="quantity[]" id="quantity` +
                newKey + `" onchange="chkItemPrice(` + newKey + `)" onkeyup="chkItemPrice(` + newKey + `)" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="unit_price[]" id="unit_price` +
                newKey + `" onkeyup="chkItemPrice(` + newKey +
                `)">
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control total_price" name="amount[]" id="amount` +
                newKey + `" readonly>
                </td>
            </tr>`;

            $('#responseHtml').append(html);
            $('#product_id' + newKey).val('');
            $('#quantity' + newKey).val('');

            $('.select2').select2({
                width: '100%',
                placeholder: 'Select',
                tag: true
            });
        }

        function removeRow(key) {
            $('#row' + key).remove();
        }

        function checkItemQuantity(key) {
            var quantity = Number($('#quantity' + key).val());

            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();

                alerts('Please Provide Valid Quantity!');
            }
        }

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
            var total =  Number(subTotal + shipping_charge - discount_amount);
            var remainning_due =  Number(total - advance_amount);
            $('#total_amount').val(Number(total).toFixed(2));
            $('#remainning_due').val(Number(remainning_due).toFixed(2));
        }

        $( document ).ready(function() {
            $('#type').on('change', function(){
                let role = $(this).val();
                $("#customer_id").select2().val('').trigger("change");
                // $("#shipping_rate_id").select2().val('').trigger("change");
                // $("#shipping_charge").val(0);
                totalCal();
                if ( role == "Admin") {
                    $('#user').hide();
                    $('#reseller_business').hide();
                    $('#customer').show();
                    $('#customer_id').html('');
                    var id = $('#customer_id').val();
                    $.ajax({
                        url: '{{ route('admin.user.getcustomer') }}',
                        type: "GET",
                        dataType: 'json',
                        data: {
                            id: id,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var html = '';
                            $( response.data ).each(function( index , val ) {
                                html += ` <option value="${val.id}"> ${val.name} - ${val.mobile}</option> `;
                            });
                            $('#customer_id').html(html);
                        }
                    })
                } else if ( role == "Reseller Business") {
                    $('#user').hide();
                    $('#customer').hide();
                    $('#reseller_business').show();
                    $.ajax({
                        url: '{{ route('admin.user.getreseller.business') }}',
                        type: "GET",
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var html = '';
                            html += ` <option value=""> Select Reseller Business</option> `;
                            $( response.data ).each(function( index , val ) {
                                html += ` <option value="${val.id}"> ${val.name}</option> `;
                            });
                            $('#reseller_business_id').html(html);
                        }
                    })
                } else {
                        $('#user').show();
                        $('#reseller_business').hide();
                        $('#customer').show();
                        $.ajax({
                        url: '{{ route('admin.user.getuser') }}',
                        type: "GET",
                        dataType: 'json',
                        data: {
                            role: role,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var html = '';
                            html += `<option value="">Select</option>`;
                            $( response.data ).each(function( index , val ) {
                                html += ` <option value="${val.id}"> ${val.name}</option> `;
                            });
                            $('#user_id').html(html);
                        }
                    })
                }
            });
            $('#user_id').on('change', function(){
                let id = $(this).val();
                    $.ajax({
                    url: '{{ route('admin.user.getcustomer') }}',
                    type: "GET",
                    dataType: 'json',
                    data: {
                        id: id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var html = '';
                        html += `<option value="">Select</option>`;
                        $( response.data ).each(function( index , val ) {
                            html += ` <option value="${val.id}"> ${val.name} - ${val.mobile}</option> `;
                        });
                        $('#customer_id').html(html);
                    }
                })
            });
            $('#customer_id').on('change', function(){
                let id = $(this).val();
                $.ajax({
                    url: '{{ route('admin.user.getsinglecustomer') }}',
                    type: "GET",
                    dataType: 'json',
                    data: {
                        id: id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $("#customer_name").html(response.data.name);
                            $("#customer_mobile_number").html(response.data.mobile);
                            $("#customer_address").html(response.data.address);
                            $("#customer_due").html(response.due);
                            var url = "{{ route('admin.orders.show', ':id') }}";
                            var html = '';
                            $(response.data.orders).each(function(index, val) {
                                var orderUrl = url.replace(':id', val.id);
                                html += `<a href="${orderUrl}">${val.code}-${val.date}</a> `;
                            });
                            $("#customer_orders").html(html);
                        }
                    }
                })
            });
        });

        // $('#shipping_rate_id').on('change', function(){
        //         let id = $(this).val();
        //         getShippingCharge(id);
        // });
        
        //  function getShippingCharge(key) {
        //     $.ajax({
        //             url: '{{ route('admin.basic.shippingcharge') }}',
        //             type: "GET",
        //             dataType: 'json',
        //             data: {
        //                 id: key,
        //             },
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function(response) {
        //               $('#shipping_charge').val(response.data.rate);
        //               totalCal();
        //             }
        //         })
        // }
    </script>
@endpush