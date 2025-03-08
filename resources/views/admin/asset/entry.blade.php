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
                    <a href="{{ route('admin.asset.asset-items.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Asset Entry List
                    </a>
                </li>

                @can('add asset-item')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.asset.asset-items.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Asset Entry
                        </a>
                    </li>
                @endcan

                @can('edit asset-item')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Asset Entry
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show asset-item')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Asset Entry Detail
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
                                    <th>Asset Name</th>
                                    <th>:</th>
                                    <td>{{ $data->asset != null ? $data->asset->name : '-'  }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <th>:</th>
                                    <td>{{ number_format($data->quantity, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Price</th>
                                    <th>:</th>
                                    <td>{{ number_format($data->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Price</th>
                                    <th>:</th>
                                    <td>{{ number_format($data->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @elseif(isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ route('admin.asset.asset-items.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ old('date', date('d-m-Y')) }}"
                                                    required>

                                                @if ($errors->has('date'))
                                                    <span class="help-block">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Note :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="note"
                                                    value="{{ old('note') }}">

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
                                                <th class="required">Asset Name</th>
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
                                                        <select name="asset_id[]" id="asset_id{{ $key }}"
                                                            class="form-control select2"
                                                            onchange="checkAsset({{ $key }})" required>
                                                            <option value="">Select Asset</option>
                                                            @foreach ($assets as $asset)
                                                                <option value="{{ $asset->id }}"
                                                                    {{ $item->asset_id == $asset->id ? 'selected' : '' }}>
                                                                    {{ $asset->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control qty" name="quantity[]"
                                                            id="quantity{{ $key }}"
                                                            value="0"
                                                            onclick="chkItemPrice({{ $key }})"
                                                            onkeyup="chkItemPrice({{ $key }})" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="price[]" id="price{{ $key }}"
                                                            value="0"
                                                            onkeyup="chkItemPrice({{ $key }})" onclick="chkItemPrice({{ $key }})">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control total_price"
                                                            name="amount[]" id="amount{{ $key }}"
                                                            value="0" readonly>
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
                                                        value="0">
                                                </td>
                                                <td class="text-right"><strong>Total Amount
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_amount" id="total_amount"
                                                        value="0">
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
                    @elseif(isset($edit))
                        @include('admin.asset.inc.edit')
                    @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.asset.asset-items.index') }}" class="form-inline">
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
                                            href="{{ route('admin.asset.asset-items.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
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
                                            <td>{{ $val->asset->name ?? '' }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td>{{ number_format($val->quantity, 2) }}</td>
                                            <td> {{ number_format($val->price, 2) }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show asset-item')
                                                            <li><a href="{{ route('admin.asset.asset-items.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                        @can('edit asset-item')
                                                            <li><a href="{{ route('admin.asset.asset-items.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan
                                                        @can('delete asset-item')
                                                            <li><a onclick="deleted('{{ route('admin.asset.asset-items.destroy', $val->id) . qString() }}')"><i
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
       
        function checkAsset(key) {
            var asset_id = $('#asset_id' + key).val();
            var rowId = $(".subRow").length;

            var assetOptions = $('#asset_id' + key).html();
            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#asset_id' + x).val() == asset_id) {
                        $('#asset_id' + key).html(assetOptions);
                        alerts('This Asset Already Entered In This Asset Entry.');
                        return false;
                    }
                }
            }
        }

        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var assetOptions = $('#asset_id' + key).html();
            var quantity = $('#quantity' + key).val();

            var html = `<tr class="subRow" id="row` + newKey + `">
                <td><a class="btn btn-danger btn-flat" onclick="removeRow(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
                <td>
                    <select name="asset_id[]" id="asset_id` + newKey +
                `" class="form-control select2" required onchange="checkAsset(` + newKey + `)">` + assetOptions +
                `</select>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control qty" name="quantity[]" id="quantity` +
                newKey + `" onchange="chkItemPrice(` + newKey + `)" onkeyup="chkItemPrice(` + newKey + `)" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="price[]" id="price` +
                newKey + `" onkeyup="chkItemPrice(` + newKey +
                `)">
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control total_price" name="amount[]" id="amount` +
                newKey + `" readonly>
                </td>
            </tr>`;

            $('#responseHtml').append(html);
            $('#asset_id' + newKey).val('');
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
            var price = Number($('#price' + key).val());

            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Please Provide Valid Quantity!');
            }
            if (quantity > 0 && price > 0)
            var total = Number(quantity * price);
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
            $('#total_amount').val(Number(subTotal).toFixed(2));
        }
    </script>
@endpush
