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
                    <a href="{{ route('admin.purchase.order-base-turkey.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Order Base Turkey List
                    </a>
                </li>

                @can('add purchase')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.purchase.order-base-turkey.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Order Base Turkey
                        </a>
                    </li>
                @endcan

                @can('edit purchase')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Order Base Turkey
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show purchase')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Order Base Turkey Detail
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
                                <tr>
                                    <th>Order Code</th>
                                    <th>:</th>
                                    <td>{{ $data->order->code }}</td>
                                </tr>
                                <tr>
                                    <th>Supplier</th>
                                    <th>:</th>
                                    <td>{{ $data->supplier != null ? $data->supplier->name : '' }}</td>
                                </tr>

                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->date) }}</td>
                                </tr>

                                <tr>
                                    <th>Challan Number</th>
                                    <th>:</th>
                                    <td>{{ $data->challan_number }}</td>
                                </tr>

                                @if ($data->challan_image)
                                    <tr>
                                        <th>Challan Image</th>
                                        <th>:</th>
                                        <td>
                                            <div class="col-md-4 col-sm-6">
                                                {!! MediaUploader::showImg('challan', $data->challan_image, ['class' => 'img-responsive']) !!}
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <th>Type</th>
                                    <th>:</th>
                                    <td>{{ $data->type ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{{ $data->note ?? '' }}</td>
                                </tr>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>Color</th>
                                        <th style="text-align: right;">Quantity</th>
                                        <th style="text-align: right;">Used Quantity</th>
                                        <th style="text-align: right;">Unit Price</th>
                                        <th style="text-align: right;">Fabric Quantity</th>
                                        <th style="text-align: right;">Fabric Unit</th>
                                        <th style="text-align: right;">Fabric Unit Price</th>
                                        <th style="text-align: right;">Total Price</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($data->items as $key => $val)
                                        <tr>
                                            <td>{{ $val->product != null ? $val->product->name : '-' }}</td>
                                            <td>{{ $val->unit != null ? $val->unit->name : '-' }}</td>
                                            <td>{{ $val->color != null ? $val->color->name : '-' }}</td>
                                            <td style="text-align: right;">{{ number_format($val->quantity, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->used_quantity, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->unit_price, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->fabric_quantity, 2) }}</td>
                                            <td style="text-align: right;">{{ $val->fabricUnit->name }}</td>
                                            <td style="text-align: right;">{{ number_format($val->fabric_unit_price, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;" colspan="6">Total Quantity :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->items->sum('quantity'), 2) }}
                                        </th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->items->sum('used_quantity'), 2) }}
                                        </th>
                                        <th style="text-align: right;">Sub Total Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->subtotal_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="9">Vat
                                            ({{ number_format($data->vat_percent, 2) }}%):</th>
                                        <th style="text-align: right;">{{ number_format($data->vat_amount, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="9">Cost:</th>
                                        <th style="text-align: right;">{{ number_format($data->cost, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="9">Adj. Amount:</th>
                                        <th style="text-align: right;">{{ number_format($data->adjust_amount, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="9">Total Amount :</th>
                                        <th style="text-align: right;">{{ number_format($data->total_amount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.purchase.order-base-turkey.update', $edit) : route('admin.purchase.order-base-turkey.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-md-8">

                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Supplier :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="supplier_id" required>
                                                    <option value="">Select Supplier</option>
                                                    @php($supplier_id = old('supplier_id', isset($data) ? $data->supplier_id : ''))
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ $supplier_id == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->name }} </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('supplier_id'))
                                                    <span class="help-block">{{ $errors->first('supplier_id') }}</span>
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
                                                    <span class="help-block">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('challan_number') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Challan No. :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="challan_number"
                                                    value="{{ old('challan_number', isset($data) ? $data->challan_number : '') }}">

                                                @if ($errors->has('challan_number'))
                                                    <span class="help-block">{{ $errors->first('challan_number') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('challan_image') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Challan Image :</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="challan_image">

                                                @if ($errors->has('challan_image'))
                                                    <span class="help-block">{{ $errors->first('challan_image') }}</span>
                                                @endif
                                                @if (isset($data))
                                                    @if ($data->challan_image)
                                                    {!! MediaUploader::showImg('challan', $data->challan_image, ['class' => 'img-responsive show_image']) !!} 
                                                    @endif
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
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-sm-3"></label>
                                        <div class="col-sm-8" style="padding: 0px !important">
                                            <input type="text" class="form-control" id="order_code" placeholder="Search Order">
                                            <input type="hidden" class="form-control" id="order_id" name="order_id" value="{{ old('order_id', isset($data) ? $data->order_id : '') }}">
                                        </div>
                                        <div class="col-sm-1">
                                            <a  id="orderSearch" class="btn btn-info">Search Order</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-body table-responsive" id="table" style="{{isset($data) ? 'display: block' : 'display: none' }}">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th class="required">Product</th>
                                                <th >Unit</th>
                                                <th >Order Qty</th>
                                                <th >Stock Qty</th>
                                                <th >Unit Price</th>
                                                <th >Fabric Unit</th>
                                                <th >Total Fabric Qty</th>
                                                <th >Fabric Stock Price</th>
                                                <th class="required">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="responseHtml">
                                            @if(isset($data))
                                                @foreach ($items as $key => $item)
                                                    <tr  style="{{isset($data) ? '' : 'display: none' }}">
                                                        <td> {{ isset($data) ? $data->code : ''}}</td>
                                                        <td>
                                                            <input type="text"  id="product_id{{ $key }}" class="form-control" readonly value="{{ optional($item->product)->name}}">
                                                            <input type="hidden" name="product_id[]"  id="product{{ $key }}" class="form-control" value="{{  optional($item->product)->id}}">
                                                            <input type="hidden" name="color_id[]"  id="color{{ $key }}" class="form-control" value="{{ optional($item->color)->id}}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" readonly value="{{ optional($item->unit)->name }}">
                                                            <input type="hidden"name="unit_id[]"  class="form-control" value="{{ optional($item->unit)->id }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" name="quantity[]"
                                                                id="quantity{{ $key }}"
                                                                value="{{ $item->quantity }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" name="stock_quantity[]"
                                                                id="quantity{{ $key }}"
                                                                value="{{ $item->product->stock_quantity }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control"
                                                                name="unit_price[]" id="unit_price{{ $key }}"
                                                                value="{{ $item->unit_price }}"
                                                                 readonly>
                                                        </td>
                                                        
                                                        <td>
                                                            <input type="text" class="form-control" readonly value="{{ optional($item->fabricUnit)->name }}" readonly>
                                                            <input type="hidden"name="fabric_unit_id[]"  class="form-control" value="{{ optional($item->fabricUnit)->id }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" name="fabric_quantity[]"
                                                                id="fabric_quantity{{ $key }}" value="{{ $item->fabric_quantity }}" onchange="chkItemPrice({{ $key}})" onkeyup="chkItemPrice({{ $key}})">
                                                        </td>
                                                        <td>
                                                            <input type="decimal" class="form-control"
                                                                name="fabric_unit_price[]" id="fabric_unit_price{{ $key }}"
                                                                value="{{ $item->fabric_unit_price }}" onchange="chkItemPrice({{ $key}})" onkeyup="chkItemPrice({{ $key}})">
                                                        </td>
                                                        <td>
                                                            <input type="decimal" class="form-control total_price"
                                                                name="amount[]" id="amount{{ $key }}"
                                                                value="{{ $item->total_price }}" readonly>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td class="text-right" colspan="7"><strong>Total Quantity
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_quantity" id="total_quantity"
                                                        value="{{ isset($edit) ? $items->sum('quantity') : '' }}">
                                                </td>
                                                <td class="text-right"><strong>Sub Total Amount
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="subtotal_amount" id="subtotal_amount"
                                                        value="{{ isset($edit) ? numberFormat($items->sum('total_price')) : '' }}">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-right" colspan="9">
                                                    <strong>Vat({{ isset($data) ? $data->vat_percent : env('VAT_PERCENT') }}%)
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="number" class="form-control"
                                                        name="vat_percent" onkeyup="totalCal()" id="vat_percent"
                                                        value="{{ old('vat_percent', isset($data) ? $data->vat_percent : env('VAT_PERCENT')) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="9">
                                                    <strong>Vat Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        name="vat_amount" id="vat_amount" readonly
                                                        value="{{ old('vat_amount', isset($data) ? $data->vat_amount : '') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="9">
                                                    <strong>Cost :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" onkeyup="totalCal()" class="form-control"
                                                        name="cost" id="cost"
                                                        value="{{ old('cost', isset($data) ? $data->cost : '') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="9">
                                                    <strong>Adj. Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" onkeyup="totalCal()" class="form-control"
                                                        name="adjust_amount" id="adjust_amount"
                                                        value="{{ old('adjust_amount', isset($data) ? $data->adjust_amount : '') }}">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-right" colspan="9"><strong>Total Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_amount" id="total_amount"
                                                        value="{{ old('total_amount', isset($data) ? $data->total_amount : '') }}">
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="form-group" id="button" style="{{isset($data) ? 'display: block' : 'display: none' }}">
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
                        <form method="GET" action="{{ route('admin.purchase.order-base-turkey.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select class="form-control" name="supplier_id">
                                            <option value="">Any Supplier</option>
                                            @foreach ($suppliers as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ Request::get('supplier_id') == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name }} </option>
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
                                            href="{{ route('admin.purchase.order-base-turkey.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Order Code</th>
                                        <th>Supplier</th>
                                        <th>Date</th>
                                        <th>Challan Number</th>
                                        <th>Items</th>
                                        <th>Adj. Amount</th>
                                        <th>Total Amount</th>
                                        <th>Created By</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ $val->order->code }}</td>
                                            <td>{{ $val->supplier->name ?? '' }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->challan_number }}</td>
                                            <td>
                                                @foreach ($val->items as $key => $item)
                                                    {{ $item->product->name ?? '-' }}
                                                    <span
                                                        class="label label-default">{{ number_format($item->quantity, 0) }} {{ $item->unit ? $item->unit->name : '' }} {{ $item->color ? $item->color->name : '' }}</span>
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
                                            <td> {{ number_format($val->adjust_amount, 2) }}</td>
                                            <td>{{ number_format($val->total_amount, 2) }}</td>
                                            <td>{{ isset($val->createdBy) ? $val->createdBy->name : '' }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show purchase')
                                                            <li><a href="{{ route('admin.purchase.order-base-turkey.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                       
                                                        {{-- @can('edit purchase')
                                                            <li><a href="{{ route('admin.purchase.order-base-turkey.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan --}}

                                                        {{-- @can('delete purchase')
                                                            <li><a onclick="deleted('{{ route('admin.purchase.order-base-turkey.destroy', $val->id) . qString() }}')"><i
                                                                        class="fa fa-close"></i> Delete</a></li>
                                                        @endcan --}}
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
       $('#orderSearch').on('click', function() {
            var order_code = $('#order_code').val();
            $.ajax({
                url: '{{ route('admin.getorder') }}',
                type: "GET",
                dataType: 'json',
                data: {
                    code: order_code,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#table').show();
                        $('#button').show();
                        var html = '';
                        var subtotal_amount = $('#subtotal_amount').val();
                        if (!subtotal_amount) {
                            subtotal_amount = 0;
                        }
                        var total_quantity = $('#total_quantity').val();
                        if (!total_quantity) {
                            total_quantity = 0;
                        }
                         var key = 0;
                         console.log(response.data);
                        $(response.data.items).each(function( index , val ) {
                            
                            if (val.product.product_type == 'Combo') {
                                $(val.product_bases).each(function( i , v ) {
                                    key++
                                var product =  v.product.name;
                                var product_id =  v.product.id;
                                var unit_price = v.product.stock_price;
                                var stock_qty = v.product.stock_quantity;
                                var order_quantity = val.quantity * v.quantity;
                                var need_quantity = val.quantity * v.quantity;
                                if (stock_qty >= need_quantity) {
                                    need_quantity = 0;
                                } else {
                                    need_quantity -= stock_qty;
                                }
                                var unit =  val.unit_id ? val.unit.name: '';
                                var unit_id =  val.unit_id ? val.unit.id: '';
                                var color =  val.color_id ? val.color.name: '';
                                var color_id =  val.color_id ? val.color.id: '';
                                var fabric_quantity =  v.product_fabric.fabric_quantity * order_quantity;
                                var fabric_stock_price =  val.product_fabric.fabric.stock_price;
                                var amount =  val.product_fabric.fabric.stock_price * v.product_fabric.fabric_quantity * order_quantity;
                                var fabric_unit_name =  v.product_fabric.fabric_unit.name;
                                var fabric_unit_id =  v.product_fabric.fabric_unit.id;
                                subtotal_amount += Number(amount);
                                total_quantity += Number((fabric_quantity));
                                html += `<tr class="subRow">
                                    <td>
                                    ${response.data.code}
                                    </td>
                                    <td>
                                        <input type="text"  id="product_id${key}" class="form-control" readonly value="${product}(${color})(${unit_price})(${order_quantity})">
                                        <input type="hidden" name="product_id[]"  id="product${key}" class="form-control" value="${product_id}">
                                        <input type="hidden" name="color_id[]"  id="color${key}" class="form-control" value="${color_id}">
                                    </td>
                                    
                                    <td>
                                        <input type="text" class="form-control" readonly value="${unit}">
                                        <input type="hidden" name="unit_id[]" class="form-control" value="${unit_id}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="quantity[]" readonly value="${order_quantity}">
                                    </td>

                                    <td>
                                        <input type="text" class="form-control" name="stock_quantity[]" readonly value="${stock_qty}">
                                    </td>

                                    <td>
                                        <input type="text" class="form-control" name="unit_price[]" readonly value="${unit_price}">
                                    </td>

                                    <td>
                                        <input type="text" class="form-control" readonly value="${fabric_unit_name}">
                                        <input type="hidden" name="fabric_unit_id[]" class="form-control" value="${fabric_unit_id}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="fabric_quantity[]"
                                            id="fabric_quantity${key}"
                                            value="${fabric_quantity}" onchange="chkItemPrice(${key})" onkeyup="chkItemPrice(${key})">
                                    </td>
                                    <td>
                                        <input type="decimal" class="form-control" name="fabric_unit_price[]" id="fabric_unit_price${key}"
                                             value="${fabric_stock_price}" onchange="chkItemPrice(${key})" onkeyup="chkItemPrice(${key})">
                                    </td>
                                    <td>
                                        <input type="decimal" class="form-control total_price"
                                            name="amount[]" id="amount${key}"
                                            value="${amount}" readonly>
                                    </td>
                                </tr>`;
                                });
                            }else{
                                key++
                                var product =  val.product_id ? val.product.name: '';
                                var unit_price = val.product_id ? val.product.stock_price: '';
                                var unit =  val.unit_id ? val.unit.name: '';
                                var color =  val.color_id ? val.color.name: '';
                                var color_id =  val.color_id ? val.color.id: '';
                                var stock_qty = val.product.stock_quantity;
                                var order_quantity = val.quantity;
                                var need_quantity = val.quantity;
                                if (stock_qty >= need_quantity) {
                                    need_quantity = 0;
                                } else {
                                    need_quantity -= stock_qty;
                                }
                                var fabric_quantity =  val.product_fabric.fabric_quantity * order_quantity;
                                var fabric_stock_price =  val.product_fabric.fabric.stock_price;
                                var amount =  val.product_fabric.fabric.stock_price * val.product_fabric.fabric_quantity * order_quantity;
                                var fabric_unit_name =  val.product_fabric.fabric_unit.name;
                                var fabric_unit_id =  val.product_fabric.fabric_unit.id;
                               
                                subtotal_amount += Number(amount);
                                total_quantity += Number(fabric_quantity);
                                html += `<tr class="subRow">
                                    <td>
                                    ${response.data.code}
                                    </td>
                                    <td>
                                        <input type="text"  id="product_id${key}" class="form-control" readonly value="${product}(${unit})(${color})">
                                        <input type="hidden" name="product_id[]"  id="product${key}" class="form-control" value="${val.product_id}">
                                        <input type="hidden" name="color_id[]"  id="color${key}" class="form-control" value="${color_id}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" readonly value="${unit}">
                                        <input type="hidden" name="unit_id[]" class="form-control" value="${val.unit_id}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="quantity[]"
                                            id="quantity${key}"
                                            value="${val.quantity}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="stock_quantity[]" readonly value="${stock_qty}">
                                    </td>
                                    <td>
                                        <input type="decimal" class="form-control" name="unit_price[]" id="unit_price${key}"
                                             value="${val.product.stock_price}" readonly>
                                    </td>


                                    <td>
                                        <input type="text" class="form-control" readonly value="${fabric_unit_name}">
                                        <input type="hidden" name="fabric_unit_id[]" class="form-control" value="${fabric_unit_id}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="fabric_quantity[]"
                                            id="fabric_quantity${key}"
                                            value="${fabric_quantity}" onchange="chkItemPrice(${key})" onkeyup="chkItemPrice(${key})">
                                    </td>
                                    <td>
                                        <input type="decimal" class="form-control" name="fabric_unit_price[]" id="fabric_unit_price${key}"
                                             value="${fabric_stock_price}" onchange="chkItemPrice(${key})" onkeyup="chkItemPrice(${key})">
                                    </td>
                                    <td>
                                        <input type="decimal" class="form-control total_price"
                                            name="amount[]" id="amount${key}"
                                            value="${amount}" readonly>
                                    </td>
                                </tr>`;
                            }
                        });
                        $('#responseHtml').append(html);
                        $('#total_quantity').val(total_quantity);
                        $('#subtotal_amount').val(Number(subtotal_amount).toFixed(2));
                        $('#total_amount').val(Number(subtotal_amount).toFixed(2));
                        $('#order_code').val('');
                        $('#order_id').val(response.data.id);
                        $('.select2').select2({
                            width: '100%',
                            placeholder: 'Select',
                            tag: true
                        });
                    }else{
                        alert(response.data);
                    }
                }
            });
       });

        function chkItemPrice(key) {
            var quantity = Number($('#fabric_quantity' + key).val());
            var unit_price = Number($('#fabric_unit_price' + key).val());
            if (isNaN(quantity)) {
                $('#fabric_quantity' + key).val('');
                $('#fabric_quantity' + key).focus();
                alerts('Please Provide Valid Quantity!');
            }
            var total = Number(quantity * unit_price);
            $('#amount' + key).val(Number(total).toFixed(2));
            var totalQuantity = 0;
            $("input[id^='fabric_quantity']").each(function() {
                totalQuantity += +$(this).val();
                });
            $('#total_quantity').val(totalQuantity);
            totalCal();
        }
        function totalCal() {
            var subTotal = 0;
            $("input[id^='amount']").each(function() {
                subTotal += Number($(this).val());
            });
            $('#subtotal_amount').val(Number(subTotal).toFixed(2));

            var taxAmount = 0;
            var taxPercent = $('#vat_percent').val();
            if (taxPercent > 0) {
                taxAmount = ((subTotal * taxPercent) / 100);
            }
            $('#vat_amount').val(Number(taxAmount).toFixed(2));
            var adjust_amount = Number($('#adjust_amount').val());
           var cost = Number($('#cost').val());
            var total = ((subTotal + taxAmount + cost - adjust_amount));
            $('#total_amount').val(Number(total).toFixed(2));
        }
    </script>
@endpush
