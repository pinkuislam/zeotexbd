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
                    <a href="{{ route('admin.purchase.raw.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Fabrics Purchase List
                    </a>
                </li>

                @can('add purchase')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.purchase.raw.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Fabrics Purchase
                        </a>
                    </li>
                @endcan

                @can('edit purchase')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Fabrics Purchase
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show purchase')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Fabrics Purchase Detail
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
                                        <th style="text-align: right;">Stock Price</th>
                                        <th style="text-align: right;">Total Price</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($data->items as $key => $val)
                                        <tr>
                                            <td>{{ $val->product != null ? $val->product->name : '-' }}</td>
                                            <td>{{ $val->unit != null ? $val->unit->name : '' }} </td>
                                            <td>{{ $val->color != null ? $val->color->name : '-' }}</td>
                                            <td style="text-align: right;">{{ number_format($val->quantity, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->used_quantity, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->unit_price, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;" colspan="3">Total Quantity :</th>
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
                                        <th style="text-align: right;" colspan="6">Vat
                                            ({{ number_format($data->vat_percent, 2) }}%):</th>
                                        <th style="text-align: right;">{{ number_format($data->vat_amount, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="6">Cost:</th>
                                        <th style="text-align: right;">{{ number_format($data->cost, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="6">Adj. Amount:</th>
                                        <th style="text-align: right;">{{ number_format($data->adjust_amount, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="6">Total Amount :</th>
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
                                action="{{ isset($edit) ? route('admin.purchase.raw.update', $edit) : route('admin.purchase.raw.store') }}{{ qString() }}"
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

                                <div class="box-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="required">Product</th>
                                                <th class="required">Unit</th>
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
                                                                <option data-unit_id="{{ $product->unit->id }}" data-unit_name="{{ $product->unit->name }}" data-unit_price="{{ $product->stock_price }}"
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
                                                            value="{{ $item->total_price }}" readonly>
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
                                                <td class="text-right"><strong>Sub Total Amount
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="subtotal_amount" id="subtotal_amount"
                                                        value="{{ isset($edit) ? numberFormat($items->sum('total_price')) : '' }}">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-right" colspan="6">
                                                    <strong>Vat({{ isset($data) ? $data->vat_percent : env('VAT_PERCENT') }}%)
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="number" class="form-control"
                                                        name="vat_percent" onkeyup="totalCal()" id="vat_percent"
                                                        value="{{ old('vat_percent', isset($data) ? $data->vat_percent : env('VAT_PERCENT')) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6">
                                                    <strong>Vat Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        name="vat_amount" id="vat_amount" readonly
                                                        value="{{ old('vat_amount', isset($data) ? $data->vat_amount : '') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6">
                                                    <strong>Adj. Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" onkeyup="totalCal()" class="form-control"
                                                        name="adjust_amount" id="adjust_amount"
                                                        value="{{ old('adjust_amount', isset($data) ? $data->adjust_amount : '') }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6">
                                                    <strong>Cost :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" onkeyup="totalCal()" class="form-control"
                                                        name="cost" id="cost"
                                                        value="{{ old('cost', isset($data) ? $data->cost : '') }}">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-right" colspan="6"><strong>Total Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_amount" id="total_amount"
                                                        value="{{ old('total_amount', isset($data) ? $data->total_amount : '') }}">
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
                        <form method="GET" action="{{ route('admin.purchase.raw.index') }}" class="form-inline">
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
                                            href="{{ route('admin.purchase.raw.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
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
                                            <td> {{ number_format($val->total_amount, 2) }}</td>
                                            <td>{{ isset($val->createdBy) ? $val->createdBy->name : '' }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show purchase')
                                                            <li><a href="{{ route('admin.purchase.raw.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                        @can('edit purchase')
                                                            <li><a href="{{ route('admin.purchase.raw.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan
                                                        @can('delete purchase')
                                                            <li><a onclick="deleted('{{ route('admin.purchase.raw.destroy', $val->id) . qString() }}')"><i
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
            var unitOptions = $('#unit_id' + key).val();
            var unit_id = $('#unit' + key).val();
            var colorOptions = $('#color_id' + key).html();
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
            totalCal();
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
