@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ (isset($list)) ? 'class=active' : '' }}>
                <a href="{{ route('admin.damage.finished.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Finished Damage List
                </a>
            </li>
            @can('show damage')
            <li {{ (isset($create)) ? 'class=active' : '' }}>
                <a href="{{ route('admin.damage.finished.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Finished Damage
                </a>
            </li>
            @endcan
            @if (isset($edit))
            <li class="active">
                <a href="#">
                    <i class="fa fa-edit" aria-hidden="true"></i> Edit Finished Damage
                </a>
            </li>
            @endif

            @if (isset($show))
            <li class="active">
                <a href="#">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> Finished Damage Details
                </a>
            </li>
            @endif
        </ul>

        <div class="tab-content">
            @if(isset($show))
            <div class="tab-pane active">
                <div class="box-body table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:120px;">Code</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->code}}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <th>:</th>
                            <td>{{ $data->type }}</td>
                        </tr>
                        <tr>
                            <th>Note</th>
                            <th>:</th>
                            <td>{{ $data->note }}</td>
                        </tr>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="control-label">Damage Items</label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>Color</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data->items as $key => $item)
                                    <tr>
                                        <td>{{ $item->product != null ? $item->product->name : '-' }}</td>
                                        <td>{{ $item->unit != null ? $item->unit->name : '-' }}</td>
                                        <td>{{ $item->color != null ? $item->color->name : '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>    
                        </div>
                    </div>
                </div>
            </div>

            @elseif(isset($edit) || isset($create))
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ isset($edit) ? route('admin.damage.finished.update', $edit) : route('admin.damage.finished.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        @if (isset($edit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-sm-8"> 

                                <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Date :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control datepicker" name="date" value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}" required>

                                        @if ($errors->has('date'))
                                            <span class="help-block">{{ $errors->first('date') }}</span>
                                        @endif
                                    </div>
                                </div>     
                                <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3">Note :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="note" value="{{ old('note', isset($data) ? $data->note : '') }}">

                                        @if ($errors->has('note'))
                                            <span class="help-block">{{ $errors->first('note') }}</span>
                                        @endif
                                    </div>
                                </div>     
                            </div>
                        </div>
                        <div class="row">
                          
                            <div class="col-sm-12">
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="required">Product</th>
                                                <th class="required">Unit</th>
                                                <th >Color</th>
                                                <th class="required">Quantity</th>
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
                                                                <option data-unit_id="{{ $product->unit->id }}" data-unit_name="{{ $product->unit->name }}" data-unit_price="{{ $product->unit_price }}"
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
                                                            onclick="checkItemQuantity({{ $key }})"
                                                            onkeyup="checkItemQuantity({{ $key }})" required>
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
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' :'Create' }}</button>
                                <button type="reset" class="btn btn-warning btn-flat">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @elseif (isset($list))
            <div class="tab-pane active">
                <form method="GET" action="{{ route('admin.production.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <input type="text" class="form-control" name="from" id="datepickerFrom" value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="to" id="datepickerTo" value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="q" value="" placeholder="Write your search text...">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">Search</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('admin.production.index') }}">X</a>
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
                                <th>Damage Items</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($result as $val)
                            <tr>
                                <td>{{ $val->code }}</td>
                                <td>{{ dateFormat($val->date) }}</td>
                                <td>{{ $val->note }} </td><td>
                                    @foreach ($val->items as $key => $item)
                                        {{ $item->product->name ?? '-' }}
                                        <span
                                            class="label label-default">{{ number_format($item->quantity, 0)  }} {{ $item->color ? $item->color->name : '' }} </span>
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
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                            type="button" data-toggle="dropdown">Action <span
                                                class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('show damage')
                                                <li><a href="{{ route('admin.damage.finished.show', $val->id) . qString() }}"><i
                                                            class="fa fa-eye"></i> Show</a></li>
                                            @endcan
                                            @can('edit damage')
                                                <li><a href="{{ route('admin.damage.finished.edit', $val->id) . qString() }}"><i
                                                            class="fa fa-pencil"></i> Edit</a></li>
                                            @endcan
                                            @can('delete damage')
                                                <li><a onclick="deleted('{{ route('admin.damage.finished.destroy', $val->id) . qString() }}')"><i
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
                                    @foreach(paginations() as $pag)
                                        <option value="{{ qUrl(['limit' => $pag]) }}" {{ ($pag == Request::get('limit')) ? 'selected' : '' }}>{{ $pag }}</option>
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
                `" class="form-control select2" onchange="checkProduct(` + newKey + `)" >` + colorOptions +
                `</select>
                </td>
                <td>
                    <input type="number" step="any" min="0" class="form-control qty" name="quantity[]" id="quantity` +
                newKey + `" onchange="checkItemQuantity(` + newKey + `)" onkeyup="checkItemQuantity(` + newKey + `)" required>
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
        totalCal();
    }
    function totalCal() {
        var quantity = 0;
        $("input[id^='quantity']").each(function() {
            quantity += +$(this).val();
        });
        $('#total_quantity').val(quantity);
    }
    </script>
@endpush

