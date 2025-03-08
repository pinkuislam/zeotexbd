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
                    <a href="{{ route('admin.accessory.purchase.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Accessory Purchase List
                    </a>
                </li>

                @can('add accessory-purchase')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.accessory.purchase.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Accessory Purchase
                        </a>
                    </li>
                @endcan

                @can('edit accessory-purchase')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Accessory Purchase
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show accessory-purchase')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Accessory Purchase Detail
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
                                <td>{{  optional($data->supplier)->name }}</td>
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
                                <th>Note</th>
                                <th>:</th>
                                <td>{{ $data->note ?? '' }}</td>
                            </tr>
                        </table>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Accessory</th>
                                    <th style="text-align: right;">Quantity</th>
                                    <th style="text-align: right;">Unit Price</th>
                                    <th style="text-align: right;">Total Price</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data->items as $key => $val)
                                    <tr>
                                        <td>{{ optional($val->accessory)->name  }}</td>
                                        <td style="text-align: right;">{{ number_format($val->quantity, 2) }}</td>
                                        <td style="text-align: right;">{{ number_format($val->unit_price, 2) }}</td>
                                        <td style="text-align: right;">{{ number_format($val->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th style="text-align: right;">Total Quantity :</th>
                                    <th style="text-align: right;">
                                        {{ number_format($data->items->sum('quantity'), 2) }}
                                    </th>
                                    <th style="text-align: right;">Sub Total Amount :</th>
                                    <th style="text-align: right;">
                                        {{ number_format($data->items->sum('total_amount'), 2) }}
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: right;" colspan="3">Cost:</th>
                                    <th style="text-align: right;">{{ number_format($data->cost, 2) }}</th>
                                </tr>
                                <tr>
                                    <th style="text-align: right;" colspan="3">Total Amount :</th>
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
                                action="{{ isset($edit) ? route('admin.accessory.purchase.update', $edit) : route('admin.accessory.purchase.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                @if (isset($edit))
                                    @method('PUT')
                                @endif
                                <div class="row">
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
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="required">Accessory Name</th>
                                                <th class="required">Quantity</th>
                                                <th class="required">Price</th>
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
                                                        <select name="accessory_id[]" id="accessory_id{{ $key }}"
                                                            class="form-control select2"
                                                            onchange="checkAccessory({{ $key }})" required>
                                                            <option value="">Select Accessory</option>
                                                            @foreach ($accessories as $accessory)
                                                                <option value="{{ $accessory->id }}" data-unit_price="{{ $accessory->unit_price }}"
                                                                    {{ $item->accessory_id == $accessory->id ? 'selected' : '' }}>
                                                                    {{ $accessory->name }}</option>
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
                                                        <input type="number" class="form-control"
                                                            name="amount[]" id="amount{{ $key }}"
                                                            value="{{ $item->total_amount }}" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td class="text-right" colspan="2"><strong>Total Quantity
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
                                                        value="{{ isset($edit) ? numberFormat($items->sum('total_amount')) : '' }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="4">
                                                    <strong>Cost :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" onkeyup="totalCal()" class="form-control"
                                                        name="cost" id="cost"
                                                        value="{{ old('cost', isset($data) ? $data->cost : '') }}">
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-right" colspan="4"><strong>Total Amount :</strong>
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
                        <form method="GET" action="{{ route('admin.accessory.purchase.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">

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
                                            href="{{ route('admin.accessory.purchase.index') }}">X</a>
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
                                        <th>Note</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td>{{ number_format($val->total_quantity, 2) }}</td>
                                            <td> {{ number_format($val->total_amount, 2) }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show accessory-purchase')
                                                            <li><a href="{{ route('admin.accessory.purchase.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                        @can('edit accessory-purchase')
                                                            <li><a href="{{ route('admin.accessory.purchase.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan
                                                        @can('delete accessory-purchase')
                                                            <li><a onclick="deleted('{{ route('admin.accessory.purchase.destroy', $val->id) . qString() }}')"><i
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
       
        function checkAccessory(key) {
            var accessory_id = $('#accessory_id' + key).val();
            var rowId = $(".subRow").length;
            $('#unit_price' + key).val($('#accessory_id' + key).find(':selected').data('unit_price'));
            var assetOptions = $('#accessory_id' + key).html();
            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#accessory_id' + x).val() == accessory_id) {
                        $('#accessory_id' + key).html(assetOptions);
                        alerts('This Accessory Already Entered In This Accessory Purchase.');
                        return false;
                    }
                }
            }
        }

        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var AccessoryOptions = $('#accessory_id' + key).html();
            var quantity = $('#quantity' + key).val();

            var html = `<tr class="subRow" id="row` + newKey + `">
                <td><a class="btn btn-danger btn-flat" onclick="removeRow(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
                <td>
                    <select name="accessory_id[]" id="accessory_id` + newKey +
                `" class="form-control select2" required onchange="checkAccessory(` + newKey + `)">` + AccessoryOptions +
                `</select>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control qty" name="quantity[]" id="quantity` + newKey + `" onclick="chkItemPrice(` + newKey + `)" onkeyup="chkItemPrice(` + newKey + `)" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="unit_price[]" id="unit_price` + newKey + `" onclick="chkItemPrice(` + newKey + `)" onkeyup="chkItemPrice(` + newKey +`)">
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control" name="amount[]" id="amount` + newKey + `" readonly>
                </td>
            </tr>`;

            $('#responseHtml').append(html);
            $('#accessory_id' + newKey).val('');
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
            $('#amount' + key).val(total);
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

           var cost = Number($('#cost').val());
            var total = ((subTotal  + cost));
            $('#total_amount').val(Number(total).toFixed(2));

        }
    </script>
@endpush
