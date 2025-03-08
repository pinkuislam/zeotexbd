@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.basic.product-cover.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Cover Product List
                    </a>
                </li>

                @can('add product')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.basic.product-cover.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Cover Product
                        </a>
                    </li>
                @endcan

                @if (isset($edit))
                    <li class="active">
                        <a href="javascript:void(0);">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Cover Product
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="javascript:void(0);">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Cover Product Details
                        </a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width:120px;">Code</th>
                                        <th style="width:10px;">:</th>
                                        <td>{{ $data->code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <th>:</th>
                                        <td>{{ $data->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Type</th>
                                        <th>:</th>
                                        <td>{{ $data->product_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Unit</th>
                                        <th>:</th>
                                        <td>{{ $data->unit ? $data->unit->name : '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alert Quantity</th>
                                        <th>:</th>
                                        <td>{{ $data->alert_quantity }}</td>
                                    </tr>

                                    <tr>
                                        <th>Stock Price</th>
                                        <th>:</th>
                                        <td>{{ $data->product_type == 'Combo' ? $data->getStockPrice() : $data->stock_price }}</td>
                                    </tr>

                                    <tr>
                                        <th>Sale Price</th>
                                        <th>:</th>
                                        <td>{{ $data->product_type == 'Combo' ? $data->getSalePrice() : $data->sale_price }}</td>
                                    </tr>

                                    @if ($data->product_type == "Base" || $data->product_type == "Base-Ready-Production" || $data->product_type == "Combo")
                                    <tr>
                                        <th>Reseller Price</th>
                                        <th>:</th>
                                        <td>{{ $data->product_type == 'Combo' ? $data->getResellerPrice() : $data->reseller_price }}</td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <th>Created By</th>
                                        <th>:</th>
                                        <td>{{ isset($data->createdBy) ? $data->createdBy->name : '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated By</th>
                                        <th>:</th>
                                        <td>{{ isset($data->updatedBy) ? $data->updatedBy->name : '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <th>:</th>
                                        <td>{{ $data->status }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            @if ($data->product_type == "Base" || $data->product_type == "Base-Ready-Production")
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Fabric</th>
                                        <th>Fabric Unit</th>
                                        <th>Fabric Quantity</th>
                                    </tr>
                                        <tr>
                                            <td>{{ $data->item->fabric->name }}</td>
                                            <td> {{ $data->item->fabricUnit->name }}</td>
                                            <td> {{ $data->item->fabric_quantity }}</td>
                                        </tr>
                                </tbody>
                            </table>
                            @endif
                            <table class="table table-bordered">
                                <tbody>
                                    @if (count($data->items) > 0)
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                    </tr>
                                        @foreach ($data->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}</td>
                                                <td> {{ $item->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.basic.product-cover.update', $edit) : route('admin.basic.product-cover.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}"
                                            style="{{ isset($data) ? '' : 'display:none' }}">
                                            <label class="control-label col-sm-3 required">Code:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="code"
                                                    value="{{ old('code', isset($data) ? $data->code : '') }}" required
                                                    readonly>

                                                @if ($errors->has('code'))
                                                    <span class="help-block">{{ $errors->first('code') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('product_type') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Type:</label>
                                            <div class="col-sm-9">
                                                <select name="product_type" class="form-control select2 product_type" required>
                                                    <option value="">Select Type</option>
                                                    @php($product_type = old('product_type', isset($data) ? $data->product_type : ''))
                                                    @foreach (coverTypes() as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $product_type == $sts ? 'selected' : '' }}>
                                                            {{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('product_type'))
                                                    <span class="help-block">{{ $errors->first('product_type') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ old('name', isset($data) ? $data->name : '') }}" required>

                                                @if ($errors->has('name'))
                                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}" style="{{ isset($data) ? ($data->product_type == 'Base' || $data->product_type == "Base-Ready-Production" )  ? '' : 'display:none' : 'display:none'  }}" id="category">
                                            <label class="control-label col-sm-3 required">Category:</label>
                                            <div class="col-sm-9">
                                                <select name="category" id="category_id" class="form-control select2 category" required>
                                                    <option value="">Select Category</option>
                                                    @php($category = old('category', isset($data) ? $data->category_type : ''))
                                                    @foreach (categoryTypes() as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $category == $sts ? 'selected' : '' }}>
                                                            {{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('category'))
                                                    <span class="help-block">{{ $errors->first('category') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('seat_count') ? ' has-error' : '' }}" style="{{ isset($data) ? ($data->product_type == 'Base' || $data->product_type == "Base-Ready-Production" ) ? '' : 'display:none' : 'display:none'  }}" id="seat_count">
                                            <label class="control-label col-sm-3 required">Seat Count:</label>
                                            <div class="col-sm-9">
                                                <select name="seat_count" id="seat_count" class="form-control select2 seat_count" required>
                                                    <option value="">Select Seat Count</option>
                                                    @php($seat_count = old('seat_count', isset($data) ? $data->seat_count : ''))
                                                    @foreach ([1,2,3,4] as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $seat_count == $sts ? 'selected' : '' }}>
                                                            {{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('seat_count'))
                                                    <span class="help-block">{{ $errors->first('seat_count') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group" style="{{ isset($data) ? ($data->product_type == 'Base' || $data->product_type == "Base-Ready-Production" ) ? 'display: block' : 'display:none' : 'display:none' }}" id="fabric">
                                            <label class="control-label col-sm-2"></label>
                                            <div class="col-sm-10">
                                                <div class="box-body table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="required">Fabric</th>
                                                                <th class="required">Fabric Quantity</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <select name="fabric_product_id" class="form-control select2 raw_product" required id="fabric_product_id">
                                                                        @if(isset($data))
                                                                            @foreach ($fabric_products as $product)
                                                                                <option value="{{ $product->id }}"
                                                                                    {{ isset($data->item) ? ($data->item->fabric_product_id == $product->id ? 'selected' : '') : '' }}>
                                                                                    {{ $product->name . '[' . $product->unit->name . ']' }}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>

                                                                    @if ($errors->has('raw_product'))
                                                                        <span class="help-block">{{ $errors->first('raw_product') }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control quantity" name="fabric_quantity" value="{{ isset($data->item) ? $data->item->fabric_quantity : 0 }}" required>
                                                                    @if ($errors->has('fabric_quantity'))
                                                                        <span class="help-block">{{ $errors->first('fabric_quantity') }}</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                      

                                        <div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}" 
                                            id="unit"  style="{{ isset($data) ? $data->product_type == 'Combo' ? 'display:block' : '' : ''  }}">
                                            <label class="control-label col-sm-3 required">Unit:</label>
                                            <div class="col-sm-9">
                                                <select name="unit_id" id="unit_id" class="form-control select2"
                                                    required>
                                                    <option value="">Select Unit</option>
                                                    @php($unit_id = old('unit_id', isset($data) ? $data->unit_id : ''))
                                                    @foreach ($units as $base)
                                                        <option value="{{ $base->id }}"
                                                            {{ $unit_id == $base->id ? 'selected' : '' }}>
                                                            {{ $base->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('unit_id'))
                                                    <span class="help-block">{{ $errors->first('unit_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('fabric_product_id') ? ' has-error' : '' }}" id="package_fabric_product"  style="{{ isset($data) ? $data->product_type == 'Combo' ? '' : 'display:none' : 'display:none'  }}">
                                            <label class="control-label col-sm-3 required">Fabric:</label>
                                            <div class="col-sm-9">
                                                <select name="fabric_base_product_id" class="form-control select2" required id="fabric_product">
                                                    <option value="">Select</option>
                                                    @php($fabric_product_id = old('fabric_product_id', isset($data->item) ? $data->item->fabric_product_id : ''))
                                                    @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"{{ $fabric_product_id == $product->id ? 'selected' : '' }}> {{ $product->name }}</option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('fabric_product_id'))
                                                    <span class="help-block">{{ $errors->first('fabric_product_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="box-body table-responsive" style="{{ isset($data) ? $data->product_type == 'Combo' ? 'display: block' : 'display:none' : 'display:none' }}" id="package">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="required">Product</th>
                                                        <th class="required">Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="responseHtml">
                                                    @foreach ($items as $key => $item)
                                                
                                                        <tr class="subRow" id="row{{ $key }}">
                                                            <td>
                                                                <input type="hidden" name="item_id[]" value="{{$item->id}}"/>
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
                                                                    class="form-control select2" required onchange="checkProduct({{ $key }})">
                                                                    <option value="">Select Product</option>
                                                                    @if (isset($data->item))
                                                                    @foreach ($fabric_wise_base_products as $product)
                                                                        <option value="{{ $product->id }}"
                                                                            {{ isset($item->base_id) ? $item->base_id == $product->id ? 'selected' : '' : '' }}>
                                                                            {{ $product->name }}</option>
                                                                    @endforeach
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    class="form-control quantity" name="quantity[]"
                                                                    id="quantity{{ $key }}"
                                                                    value="{{ $item->quantity }}" required>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="form-group{{ $errors->has('stock_price') ? ' has-error' : '' }}"
                                            id="stock_price" style="{{ isset($data) ? ($data->product_type == 'Base' || $data->product_type == 'Fabric' || $data->product_type == "Base-Ready-Production" ) ? 'display: block' : 'display:none' : 'display:none' }}">
                                            <label class="control-label col-sm-3">Stock price:</label>
                                            <div class="col-sm-9">
                                                <input type="decimal" class="form-control" name="stock_price"
                                                    value="{{ old('stock_price', isset($data) ? $data->stock_price : '') }}">

                                                @if ($errors->has('stock_price'))
                                                    <span class="help-block">{{ $errors->first('stock_price') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('sale_price') ? ' has-error' : '' }}"
                                            id="sale_price" style="{{ isset($data) ? ($data->product_type == 'Base' || $data->product_type == "Base-Ready-Production" ) ? 'display: block' : 'display:none' : 'display:none' }}">
                                            <label class="control-label col-sm-3">Sale price:</label>
                                            <div class="col-sm-9">
                                                <input type="decimal" class="form-control" name="sale_price"
                                                    value="{{ old('sale_price', isset($data) ? $data->sale_price : '') }}">

                                                @if ($errors->has('sale_price'))
                                                    <span class="help-block">{{ $errors->first('sale_price') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('reseller_price') ? ' has-error' : '' }}" id="reseller_price" 
                                            style="{{ isset($data) ? ($data->product_type == 'Base' || $data->product_type == "Base-Ready-Production") ? 'display: block' : 'display:none' : 'display:none' }}">
                                            <label class="control-label col-sm-3">Reseller price:</label>
                                            <div class="col-sm-9">
                                                <input type="decimal" class="form-control" name="reseller_price"
                                                    value="{{ old('reseller_price', isset($data) ? $data->reseller_price : '') }}">

                                                @if ($errors->has('reseller_price'))
                                                    <span class="help-block">{{ $errors->first('reseller_price') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('alert_quantity') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Alert Quantity:</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" name="alert_quantity"
                                                    value="{{ old('alert_quantity', isset($data) ? $data->alert_quantity : '') }}" required>

                                                @if ($errors->has('alert_quantity'))
                                                    <span class="help-block">{{ $errors->first('alert_quantity') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Status:</label>
                                            <div class="col-sm-9">
                                                <select name="status" class="form-control select2" required>
                                                    @php($status = old('status', isset($data) ? $data->status : ''))
                                                    @foreach (['Active', 'Deactivated'] as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('status'))
                                                    <span class="help-block">{{ $errors->first('status') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-3 text-center">
                                                <button type="submit"
                                                    class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                                <button type="reset" class="btn btn-warning btn-flat">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.basic.product-cover.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select name="product_type" class="form-control">
                                            <option value="">Any Type</option>
                                            @foreach (coverTypes() as $product_type)
                                                <option value="{{ $product_type }}"
                                                    {{ Request::get('product_type') == $product_type ? 'selected' : '' }}>
                                                    {{ $product_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <select name="category" class="form-control">
                                            <option value="">Any Category</option>
                                            @foreach (categoryTypes() as $category)
                                                <option value="{{ $category }}"
                                                    {{ Request::get('category') == $category ? 'selected' : '' }}>
                                                    {{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">Any Status</option>
                                            @foreach (['Active', 'Deactivated'] as $sts)
                                                <option value="{{ $sts }}"
                                                    {{ Request::get('status') == $sts ? 'selected' : '' }}>
                                                    {{ $sts }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" class="form-control" name="q"
                                            value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn-info btn-flat">Search</button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.basic.product-cover.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Unit</th>
                                        <th>Alert Quantity</th>
                                        <th>Stock Price</th>
                                        <th>Sale Price</th>
                                        <th>Reseller Price</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ $val->product_type }}</td>
                                            <td>{{ $val->product_type == 'Fabric' ? '' : $val->category_type }}</td>
                                            <td>{{ $val->unit ? $val->unit->name : '' }}</td>
                                            <td>{{ $val->alert_quantity }}</td>
                                            <td>{{ $val->product_type == 'Combo' ? $val->getStockPrice() : $val->stock_price }}</td>
                                            <td>{{ $val->product_type == 'Combo' ? $val->getSalePrice() : $val->sale_price }}</td>
                                            <td>{{ $val->product_type != 'Fabric' ? ($val->product_type == 'Combo' ? $val->getResellerPrice() : $val->reseller_price) : '-' }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">

                                                        @can('show product')
                                                            <li>
                                                                <a href="{{ route('admin.basic.product-cover.show', $val->id) . qString() }}">
                                                                    <i class="fa fa-eye"></i> Show
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('ecommerce_setup product')
                                                            @if (in_array($val->product_type, ['Base', 'Base-Ready-Production', 'Combo']))
                                                                <li>
                                                                    <a href="{{ route('admin.basic.cover-product.ecommerce', $val->id) . qString() }}">
                                                                        <i class="fa fa-cogs"></i> Ecommerce Setup
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endcan
                                                        @can('ecommerce_setup product')
                                                            @if (in_array($val->product_type, ['Base', 'Base-Ready-Production', 'Combo']))
                                                                <li>
                                                                    <a href="javascript:void(0);" onclick="deleted('{{ route('admin.basic.cover-products.ecommerce.destroy', $val->id).qString() }}')">
                                                                        <i class="fa fa-trash-o"></i> Ecommerce Setup Remove
                                                                    </a>
                                                            </li>
                                                            @endif
                                                        @endcan

                                                        @can('edit product')
                                                            <li><a
                                                                    href="{{ route('admin.basic.product-cover.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan

                                                        @can('status product')
                                                            <li><a
                                                                    onclick="activity('{{ route('admin.basic.cover-product.status', $val->id) . qString() }}')"><i
                                                                        class="fa fa-toggle-off"></i>
                                                                    {{ $val->status == 'Active' ? 'Deactivated' : 'Active' }}</a>
                                                            </li>
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
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($products) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $products->appends(Request::except('page'))->links() }}
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
        $(document).ready(function() {
            $(document).on('change','.product_type', function(){
                var value = $(this).val();
                if (value == 'Base' || value == 'Base-Ready-Production') {
                    $('#unit').show();
                    $('#package').hide();
                    $('#package_fabric_product').hide();
                    $('#category').show();
                    $('#fabric').show();
                    $('#stock_price').show();
                    $('#sale_price').show();
                    $('#reseller_price').show();
                    $('#seat_count').show();
                    $.ajax({
                        url: '{{ route('admin.basic.raw.product') }}',
                        type: "GET",
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var html = '';
                            html += `<option data-unit="" value="">Secect Fabric</option>`;
                            $(response.data).each(function( index , val ) {
                                html += ` <option data-unit="${val.unit_id}" value="${val.id}"> ${val.name}[${val.unit.name}] </option> `;
                            });
                            $('#fabric_product_id').html(html);
                        }
                    });
                }else if (value == 'Combo') {
                    $('#unit').show();
                    $('#package').show();
                    $('#category').show();
                    $('#stock_price').hide();
                    $('#sale_price').hide();
                    $('#reseller_price').hide();
                    $('#package_fabric_product').show();
                    $('#fabric').hide();
                    $('#seat_count').hide();
                }
                else {
                    $('#package').hide();
                    $('#category').hide();
                    $('#fabric').hide();
                    $('#stock_price').show();
                    $('#sale_price').hide();
                    $('#reseller_price').hide();
                    $('#unit').show();
                    $('#package_fabric_product').hide();
                    $('#seat_count').hide();
                }
            });
        });

        

        $(document).on('change', '#fabric_product', function(){
           var id = $(this).val();
           var category = $('#category_id').find(':selected').val();
           if (category == '') {
            alert('Please first select category');
            return false;
           }
           getProduct(id,category);
        });

        $(document).on('change', '#category_id', function(){
            
            var category = $(this).val();
            var id = $('#fabric_product').find(':selected').val();
           if (id == '') {
            return false;
           }
           getProduct(id,category);
        });

        function getProduct(id,category){
            $.ajax({
                url: '{{ route('admin.basic.base.product') }}',
                type: "GET",
                dataType: 'json',
                data: {
                    id: id,
                    category: category
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);
                    var html = '';
                    html += `<option value=""> Secect Product</option>`;
                    $( response.data ).each(function( index , val ) {
                        html += ` <option value="${val.id}"> ${val.name}</option> `;
                    });
                    $('#product_id0').html(html);
                }
            });
        }

        function checkProduct(key) {
            var product_id = $('#product_id' + key).val();
            var rowId = $(".subRow").length;
            var productOptions = $('#product_id' + key).html();

            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#product_id' + x).val() == product_id) {
                        $('#product_id' + key).html(productOptions);
                        alerts('This Product Already Entered In This Combo.');
                        return false;
                    }
                }
            }
        }

        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var productOptions = $('#product_id' + key).html();
            var html = `<tr class="subRow" id="row` + newKey + `">
                <td><a class="btn btn-danger btn-flat" onclick="removeRow(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
                <td>
                    <select name="product_id[]" id="product_id` + newKey +
                `" class="form-control select2" required onchange="checkProduct(` + newKey + `)">` + productOptions +
                `</select>
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="quantity[]" id="quantity` +
                newKey + `" required>
                </td>
            </tr>`;
            $('#responseHtml').append(html);
            $('#product_id' + newKey).val('');
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select',
                tag: true
            });
        }

        function removeRow(key) {
            $('#row' + key).remove();
        }
    </script>
@endpush
