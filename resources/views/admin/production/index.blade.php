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
                @can('show production')
                @if (isset($show))
                <li class="active">
                    <a href="#">
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Order Base Production Details
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
                                    <th style="width:120px;">Code</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->code }}</td>
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
                                            @foreach ($data->raw_items as $key => $item)
                                                <tr>
                                                    <td>{{ $item->product != null ? $item->product->name : '-' }}</td>
                                                    <td>{{ $item->product->unit != null ? $item->product->unit->name : '-' }}
                                                    </td>
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
                                            @foreach ($data->prod_items as $key => $item)
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
                @elseif(isset($edit) || isset($order))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.production.order-base.update', $edit) : route('admin.production.order-base.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">

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
                                        <div class="form-group">
                                            <label class="control-label col-sm-3"></label>
                                            <div class="col-sm-9">
                                               <div class="row">
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control" id="order_code" placeholder="Search Order">
                                                    <input type="hidden" class="form-control" id="order_id" name="order_id"
                                                        value="{{ old('order_id', isset($data) ? $data->order_id : '') }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <a id="orderSearch" class="btn btn-info">Search Order</a>
                                                </div>
                                               </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body table-responsive" id="table"
                                    style="{{ isset($data) ? 'display: block' : 'display: none' }}">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th class="required">Product</th>
                                                <th>Unit</th>
                                                <th>Order Qty</th>
                                                <th>Stock Qty</th>
                                                <th>Fabric Unit</th>
                                                <th>Fabric Stock</th>
                                                <th>Total Fabric Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="responseHtml">

                                        </tbody>
                                    </table>
                                </div>


                                <div class="form-group">
                                    <div class="text-center">
                                        <button type="submit"
                                            class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                        <button type="reset" class="btn btn-warning btn-flat">Clear</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.production.order-base.index') }}" class="form-inline">
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
                                        <input type="text" class="form-control" name="q" value=""
                                            placeholder="Write your search text...">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-info btn-flat">Search</button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.production.order-base.index') }}">X</a>
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
                                        <th>Date</th>
                                        <th>Note</th>
                                        <th>Raw Meterials</th>
                                        <th>Production Items</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($prods as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ $val->order_code }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->note }} </td>
                                            <td>
                                                @foreach ($val->raw_items as $key => $item)
                                                    {{ $item->product->name ?? '-' }}
                                                    <span
                                                        class="label label-default">{{ number_format($item->quantity, 0) }}
                                                        {{ $item->product->unit ? $item->product->unit->name : '' }}
                                                        {{ $item->color ? $item->color->name : '' }} </span>
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
                                                        class="label label-default">{{ number_format($item->quantity, 0) }}
                                                        {{ $item->color ? $item->color->name : '' }} </span>
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
                                                        @can('show production')
                                                            <li><a href="{{ route('admin.production.order-base.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
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
                                            @foreach (paginations() as $pag)
                                                <option value="{{ qUrl(['limit' => $pag]) }}"
                                                    {{ $pag == Request::get('limit') ? 'selected' : '' }}>
                                                    {{ $pag }}</option>
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
                url: '{{ route('admin.production.order') }}',
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
                        var key = 0;
                        var html = '';
                        console.log(response.data);
                        $(response.data.items).each(function(index, val) {

                            if (val.product.product_type == 'Combo') {
                                $(val.product_bases).each(function(i, v) {
                                    key++
                                    var product = v.product.name;
                                    var product_id = v.product.id;
                                    var unit_price = v.product.stock_price;
                                    var stock_qty = v.product.stock_quantity;
                                    var order_quantity = val.quantity * v.quantity;
                                    var need_quantity = val.quantity * v.quantity;
                                    if (stock_qty >= need_quantity) {
                                        need_quantity = 0;
                                    } else {
                                        need_quantity -= stock_qty;
                                    }
                                    var unit = val.unit_id ? val.unit.name : '';
                                    var unit_id = val.unit_id ? val.unit.id : '';
                                    var color = val.color_id ? val.color.name : '';
                                    var color_id = val.color_id ? val.color.id : '';
                                    var fabric_quantity = v.product_fabric
                                        .fabric_quantity * order_quantity;
                                    var fabric_stock_price = val.product_fabric.fabric
                                        .stock_price;
                                    var amount = val.product_fabric.fabric.stock_price *
                                        v.product_fabric.fabric_quantity *
                                        order_quantity;
                                    var fabric_unit_name = v.product_fabric.fabric_unit
                                        .name;
                                    var fabric_unit_id = v.product_fabric.fabric_unit
                                    .id;
                                    var fabric_stock = v.product_fabric.fabric
                                        .stock_quantity;
                                    var fabric_product_id = v.product_fabric.fabric.id;
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
                                        <input type="text" class="form-control" readonly value="${fabric_unit_name}">
                                        <input type="hidden" name="fabric_unit_id[]" class="form-control" value="${fabric_unit_id}">
                                        <input type="hidden" name="fabric_product_id[]" id="fabric_product_id${key}" class="form-control" value="${fabric_product_id}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="fabric_stock${key}" readonly value="${fabric_stock}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="fabric_quantity[]"
                                            id="fabric_quantity${key}"
                                            value="${fabric_quantity}" onchange="checkFabricStock()" onkeyup="checkFabricStock()">
                                            
                                            <input type="hidden" class="form-control" name="fabric_unit_price[]" id="fabric_unit_price${key}"
                                             value="${fabric_stock_price}">
                                    </td>
                                </tr>`;
                                });
                            } else {
                                key++
                                var product = val.product_id ? val.product.name : '';
                                var unit_price = val.product_id ? val.product.stock_price : '';
                                var unit = val.unit_id ? val.unit.name : '';
                                var color = val.color_id ? val.color.name : '';
                                var color_id = val.color_id ? val.color.id : '';

                                var stock_qty = val.product.stock_quantity;
                                var order_quantity = val.quantity;
                                var need_quantity = val.quantity;
                                if (stock_qty >= need_quantity) {
                                    need_quantity = 0;
                                } else {
                                    need_quantity -= stock_qty;
                                }

                                var fabric_quantity = val.product_fabric.fabric_quantity * order_quantity;
                                var fabric_stock_price = val.product_fabric.fabric.stock_price;
                                var amount = val.product_fabric.fabric.stock_price * val
                                    .product_fabric.fabric_quantity * order_quantity;
                                var fabric_unit_name = val.product_fabric.fabric_unit.name;
                                var fabric_unit_id = val.product_fabric.fabric_unit.id;
                                var fabric_stock = val.product_fabric.fabric.stock_quantity;
                                var fabric_product_id = val.product_fabric.fabric.id;
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
                                        <input type="text" class="form-control" readonly value="${fabric_unit_name}">
                                        <input type="hidden" name="fabric_unit_id[]" class="form-control" value="${fabric_unit_id}">
                                        <input type="hidden" name="fabric_product_id[]" id="fabric_product_id${key}" class="form-control" 
                                        value="${fabric_product_id}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="fabric_stock${key}" readonly value="${fabric_stock}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="fabric_quantity[]"
                                            id="fabric_quantity${key}"
                                            value="${fabric_quantity}" onchange="checkFabricStock()" onkeyup="checkFabricStock()">
                                            <input type="hidden" class="form-control" name="fabric_unit_price[]" id="fabric_unit_price${key}"
                                             value="${fabric_stock_price}">
                                    </td>
                                </tr>`;
                            }
                        });
                        $('#responseHtml').append(html);
                        $('#order_code').val('');
                        $('#order_id').val(response.data.id);
                        $('.select2').select2({
                            width: '100%',
                            placeholder: 'Select',
                            tag: true
                        });
                        checkFabricStock()
                    } else {
                        alert(response.data);
                    }
                }
            });
        });



        function checkFabricStock() {
            var quantity = {};
            var stock = {};
            var rowId = $(".subRow").length;
            var disabled = false;
            for (var x = 1; x <= rowId; x++) {
                if (!quantity[Number($('#fabric_product_id' + x).val())]) {
                    quantity[Number($('#fabric_product_id' + x).val())] = 0;
                }
                quantity[Number($('#fabric_product_id' + x).val())] += Number($('#fabric_quantity' + x).val());
                if (!stock[Number($('#fabric_product_id' + x).val())]) {
                    stock[Number($('#fabric_product_id' + x).val())] = 0;
                }
                stock[Number($('#fabric_product_id' + x).val())] = Number($('#fabric_stock' + x).val());
            }

            Object.entries(quantity).forEach(function([product_id, item]) {
                console.log(product_id, item, stock[product_id]);
                if (item > stock[product_id]) {
                    disabled = true;
                    var rowId = $(".subRow").length;
                    for (var x = 1; x <= rowId; x++) {
                        if ($('#fabric_product_id' + x).val() == product_id) {
                            $('#fabric_quantity' + x).val(0);
                        }

                    }
                    alert("Stock not found!");
                    $('form#are_you_sure button[type=submit]').prop('disabled', true);
                }

            });
            if (disabled == false) {
                $('form#are_you_sure button[type=submit]').prop('disabled', false);
            }




        }
    </script>
@endpush
