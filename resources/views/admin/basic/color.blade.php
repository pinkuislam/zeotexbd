@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.basic.color.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Color List
                    </a>
                </li>
                @can('add color')
                <li {{ isset($create) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.basic.color.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Color
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-upload" aria-hidden="true"></i> Import Color
                    </a>
                </li>
                @endcan

                <li>
                    <a href="{{ route('admin.basic.color.export') . qString() }}">
                        <i class="fa fa-download" aria-hidden="true"></i> Export Color
                    </a>
                </li>

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Color
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Color Details
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
                                    <th style="width:120px;">Color Code</th>
                                    <th style="width:10px;">:</th>
                                    <td>
                                        @if ($data->color_code)
                                        <input type="color" value="{{ $data->color_code }}">
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>{{ $data->status }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.basic.color.update', $edit) : route('admin.basic.color.store') }}{{ qString() }}"
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
                                        <div class="form-group{{ $errors->has('color_code') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Color Code:</label>
                                            <div class="col-sm-9">
                                                <input type="color" name="color_code"
                                                    value="{{ old('color_code', isset($data) ? $data->color_code : '') }}" required>

                                                @if ($errors->has('color_code'))
                                                    <span class="help-block">{{ $errors->first('color_code') }}</span>
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
                        <form method="GET" action="{{ route('admin.basic.color.index') }}" class="form-inline">
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
                                        <a class="btn btn-warning btn-flat" href="{{ route('admin.basic.color.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Color Code </th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($colors as $val)
                                        <tr>
                                            <td>{{ $val->name }}</td>
                                            <td> 
                                                @if ($val->color_code)
                                                <input type="color" value="{{ $val->color_code }}">
                                                @endif
                                            </td>
                                            <td>{{ $val->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show color')
                                                        <li><a href="{{ route('admin.basic.color.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @can('edit color')
                                                        <li><a href="{{ route('admin.basic.color.edit', $val->id) . qString() }}"><i class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan

                                                        @can('status color')
                                                        <li><a onclick="activity('{{ route('admin.basic.color.status', $val->id) . qString() }}')"><i class="fa fa-toggle-off"></i> {{ $val->status == 'Active' ? 'Deactivated' : 'Active' }}</a></li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.basic.color.import') . qString() }}"
                enctype="multipart/form-data" class="non-validate">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Import Color</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            Example Excel file download <a href="{{ asset('excels/colors.xlsx') }}">click here</a>
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
