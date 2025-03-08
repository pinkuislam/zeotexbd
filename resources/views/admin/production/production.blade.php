@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ isset($list) ? 'class=active' : '' }}>
                <a href="{{ route('admin.production.order-base.index') . qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Production List
                </a>
            </li>
            @can('add production')
                <li {{ (isset($create)) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.production.create').qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Production
                    </a>
                </li>
            @endcan
            @can('add production')
                <li {{ isset($order) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.production.order-base.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Order Base Production
                    </a>
                </li>
            @endcan
            @if (isset($show))
            <li class="active">
                <a href="#">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> Production  Details
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
                            <th>Note</th>
                            <th>:</th>
                            <td>{{ $data->note }}</td>
                        </tr>
                    </table>
                    <div class="row">
                        <div class="col-sm-6">
                        <label class="control-label">Uses of Raw Materials</label>
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
                            @foreach($data->raw_items as $key => $item)
                            <tr>
                                <td>{{ $item->product != null ? $item->product->name : '-' }}</td>
                                <td>{{ $item->product->unit != null ? $item->product->unit->name : '-' }}</td>
                                <td>{{ $item->color != null ? $item->color->name : '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table> 
                        
                        </div>
                        <div class="col-sm-6">
                        <label class="control-label">Production Items</label>
                        <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Color</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->prod_items as $key => $item)
                            <tr>
                                <td>{{ $item->product != null ? $item->product->name : '-' }}</td>
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
                    <form method="POST" action="{{ isset($edit) ? route('admin.production.update', $edit) : route('admin.production.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
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
                                <label class="control-label">Production Items</label>
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="required">Product</th>
                                                <th>Color</th>
                                                <th>Fabric Stock</th> 
                                                <th class="required">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="responseHtml_Prod">
                                            @foreach($prod_items as $key => $item)
                                            <tr class="subRow" id="row_prod{{$key}}">
                                                <td>
                                                    @if($key == 0)
                                                    <a class="btn btn-success btn-flat" onclick="addRowProd({{ $key }})"><i class="fa fa-plus"></i></a>
                                                    @else
                                                    <a class="btn btn-danger btn-flat" onclick="removeRowProd({{ $key }})"><i class="fa fa-minus"></i></a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <select name="product_id[]" id="product_id{{ $key }}" class="form-control select2" required onchange="checkProduct({{ $key }})">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                        <option  value="{{ $product->id }}" {{ ($item->product_id == $product->id) ? 'selected' : '' }}>{{ $product->name }} - {{ optional($product->unit)->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="color_id[]" id="color_id{{ $key }}" class="form-control select2" onchange="checkProduct({{ $key }})">
                                                        <option value="">Select Color</option>
                                                        @foreach($colors as $color)
                                                        <option  value="{{ $color->id }}" {{ ($item->color_id == $color->id) ? 'selected' : '' }}>{{ $color->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0" class="form-control" id="fabric_stock{{ $key }}" value="0"> <i class="fa fa-info-circle" id="stock_info{{ $key }}"></i>
                                                    <input type="hidden" name="stock[]" id="stock{{ $key }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0" class="form-control" name="quantity[]" id="quantity{{ $key }}" value="{{  $item->quantity ?? 0 }}" required onclick="checkStock({{ $key }})" onkeyup="checkStock({{ $key }})">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
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
                                <th>Raw Meterials</th>
                                <th>Production Items</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prods as $val)
                            <tr>
                                <td>{{ $val->code }}</td>
                                <td>{{ dateFormat($val->date) }}</td>
                                <td>{{ $val->note }} </td>
                                <td>
                                    @foreach ($val->raw_items as $key => $item)
                                        {{ $item->product->name ?? '-' }}
                                        <span
                                            class="label label-default">{{ number_format($item->quantity, 0)  }} {{ $item->product->unit ? $item->product->unit->name : '' }} {{ $item->color ? $item->color->name : '' }} </span>
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
                                    @foreach ($val->prod_items as $key => $item)
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
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('show production')
                                            <li><a href="{{ route('admin.production.order-base.show', $val->id).qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                            @endcan
                                            @can('delete production')
                                            <li><a onclick="deleted('{{ route('admin.production.order-base.destroy', $val->id).qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
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
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($prods) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $prods->appends(Request::except('page'))->links() }}
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
    var color_id = $('#color_id' + key).val();
    var rowId = $(".subRow").length;
    var productOptions = $('#product_id' + key).html();
    var colorOptions = $('#color_id' + key).html();

    for (var x = 0; x < rowId; x++) {
        if (x != key) {
            if ($('#product_id' + x).val() == product_id && $('#color_id' + x).val() == color_id) {
                $('#product_id' + key).html(productOptions);
                $('#color_id' + key).html(colorOptions);
                alerts('This Product Already Entered In Production Items.');
                return false;
            }
        }
    }
    getRawStock(key);
}

function addRowProd(key) {
    var newKey = $("tr[id^='row_prod']").length;
    var productOptions = $('#product_id' + key).html();
    var colorOptions = $('#color_id' + key).html();

    var html = `<tr class="subRow" id="row_prod` + newKey + `">
        <td><a class="btn btn-danger btn-flat" onclick="removeRowProd(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
        <td>
            <select name="product_id[]" id="product_id` + newKey + `" class="form-control select2" required  onchange="checkProduct(` + newKey + `)">` + productOptions + `</select>
        </td>
        <td>
            <select name="color_id[]" id="color_id` + newKey + `" class="form-control select2" onchange="checkProduct(` + newKey + `)">` + colorOptions + `</select>
        </td>
        <td>
            <input type="number" step="any" min="0" class="form-control" id="fabric_stock` + newKey + `" value="0"> <i class="fa fa-info-circle" id="stock_info` + newKey + `"></i>
            <input type="hidden" name="stock[]" id="stock` + newKey + `">
        </td>
        <td>
            <input type="number" step="any" min="1" class="form-control" name="quantity[]" id="quantity` + newKey + `" required onclick="checkStock(` + newKey + `)" onkeyup="checkStock(` + newKey + `)">
        </td>
    </tr>`;
    $('#responseHtml_Prod').append(html);
    $('#product_id' + newKey).val('');
    $('#color_id' + newKey).val('');
    $('.select2').select2({
        width: '100%',
        placeholder: 'Select',
        tag: true
    });
}

function removeRowProd(key) {
    $('#row_prod' + key).remove();
}


function getRawStock(key){
    var product_id =  $('#product_id'+ key).val();
    var color_id =  $('#color_id'+ key).val();
    $.ajax({
        url: '{{ route('admin.production.getrawstock') }}',
        type: "GET",
        dataType: 'json',
        data: {
            product_id: product_id,
            color_id: color_id,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            var fabric_stock = Number(response.stock);
            var fabric_per_product = Number(response.fabric_quantity);
            var stock = Number(fabric_stock / fabric_per_product);
            var info = `Fabric  ${fabric_stock} ${response.fabric_unit} , per unit fabric need ${fabric_per_product} ${response.fabric_unit} , Maximum production quantiy  ${stock} ${response.unit}`;
            
            $('#fabric_stock'+ key).val(fabric_stock);
            $('#stock'+ key).val(stock);
            $('#stock_info'+ key).attr('title',info);
        }
    });
}

function checkStock(key) {
    var quantity = Number($('#quantity' + key).val());
    var stock = Number($('#stock' + key).val());
    if (stock < quantity) {
        $('#quantity' + key).val('');
        $('#quantity' + key).focus();
        alerts('Stock quantity not exist!');
    }
}
    </script>
@endpush

