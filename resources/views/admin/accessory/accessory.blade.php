@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.accessory.accessories.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Accessory List
                    </a>
                </li>
                @can('add unit')
                <li {{ isset($create) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.accessory.accessories.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Accessory
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-upload" aria-hidden="true"></i> Import Accessory
                    </a>
                </li>
                @endcan

                <li>
                    <a href="{{ route('admin.accessory.accessories.export') . qString() }}">
                        <i class="fa fa-download" aria-hidden="true"></i> Export Accessory
                    </a>
                </li>

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Accessory
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Accessory Details
                        </a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:120px;">Name</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->name }}</td>
                                </tr>
                                <tr>
                                    <th style="width:120px;">Unit Name</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ optional($data->unit)->name }}</td>
                                </tr>
                                <tr>
                                    <th style="width:120px;">Unit Price</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->unit_price }}</td>
                                </tr>
                                <tr>
                                    <th style="width:120px;">Alert Quantity</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->alert_quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>{{ $data->status }}</td>
                                </tr>
                                <tr>
                                    <th style="width:120px;">Created By</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ optional($data->createdBy)->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.accessory.accessories.update', $edit) : route('admin.accessory.accessories.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">
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
                                        <div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}" id="unit"  style="{{ isset($data) ? $data->type == 'Package' ? 'display:block' : '' : ''  }}">
                                            <label class="control-label col-sm-3">Unit:</label>
                                            <div class="col-sm-9">
                                                <select name="unit_id" id="unit_id" class="form-control select2">
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
                                        <div class="form-group{{ $errors->has('unit_price') ? ' has-error' : '' }}" id="unit_price">
                                            <label class="control-label col-sm-3">Unit price:</label>
                                            <div class="col-sm-9">
                                                <input type="decimal" class="form-control" name="unit_price"
                                                    value="{{ old('unit_price', isset($data) ? $data->unit_price : '') }}">

                                                @if ($errors->has('unit_price'))
                                                    <span class="help-block">{{ $errors->first('unit_price') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('alert_quantity') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Alert Quantity:</label>
                                            <div class="col-sm-9">
                                                <input type="number" min="0" class="form-control" name="alert_quantity"
                                                    value="{{ old('alert_quantity', isset($data) ? $data->alert_quantity : '') }}">

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
                                                <button type="reset"
                                                    class="btn btn-warning btn-flat">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.accessory.accessories.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
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
                                        <a class="btn btn-warning btn-flat" href="{{ route('admin.accessory.accessories.index') }}">X</a>
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
                                        <th>Unit</th>
                                        <th>Unit Price</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accessories as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ optional($val->unit)->name }}</td>
                                            <td>{{ $val->unit_price }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>{{ optional($val->createdBy)->name }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show accessory')
                                                        <li><a href="{{ route('admin.accessory.accessories.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @can('edit accessory')
                                                        <li><a href="{{ route('admin.accessory.accessories.edit', $val->id) . qString() }}"><i class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan

                                                        @can('status accessory')
                                                        <li><a onclick="activity('{{ route('admin.accessory.accessories.status', $val->id) . qString() }}')"><i class="fa fa-pencil"></i> {{ $val->status == 'Active' ? 'Deactivated' : 'Active' }}</a></li>
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
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($accessories) !!}</div>
        
                            <div class="col-sm-4 text-center">
                                {{ $accessories->appends(Request::except('page'))->links() }}
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
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.accessory.accessories.import') . qString() }}"
                enctype="multipart/form-data" class="non-validate">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Import Accessory</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            Example Excel file download <a href="{{ asset('excels/accessories.xlsx') }}">click here</a>
                        </div>

                        <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                            <label class="required">xlsx:</label>
                            <input type="file" class="form-control" name="file" required>

                            @if ($errors->has('file'))
                                <span class="help-block">{{ $errors->first('file') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
