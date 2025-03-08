@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.user.investor.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Investor List
                    </a>
                </li>
                @can('add investor')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.user.investor.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Investor
                        </a>
                    </li>
                @endcan

                @can('edit investor')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Investor
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show investor')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Investor Details
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
                                    <th style="width:120px;">Name</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <th>:</th>
                                    <td>{{ $data->mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <th>:</th>
                                    <td>{!! nl2br($data->address) !!}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <th>:</th>
                                    <td>{{ $data->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <th>:</th>
                                    <td>{{ $data->updater->name ?? '-' }}</td>
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
                                action="{{ isset($edit) ? route('admin.user.investor.update', $edit) : route('admin.user.investor.store') }}{{ qString() }}"
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

                                        <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Mobile:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="mobile"
                                                    value="{{ old('mobile', isset($data) ? $data->mobile : '') }}"
                                                    >

                                                @if ($errors->has('mobile'))
                                                    <span class="help-block">{{ $errors->first('mobile') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Address:</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="address"> {{ old('address', isset($data) ? $data->address : '') }}</textarea>

                                                @if ($errors->has('address'))
                                                    <span class="help-block">{{ $errors->first('address') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Status:</label>
                                            <div class="col-sm-9">
                                                <select name="status" class="form-control select2" required>
                                                    @php($status = old('status', isset($data) ? $data->status : ''))
                                                    @foreach (['Active', 'Inactive'] as $sts)
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
                        <form method="GET" action="{{ route('admin.user.investor.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">Any Status</option>
                                            @foreach (['Active', 'Inactive'] as $sts)
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
                                        <a class="btn btn-warning btn-flat" href="{{ route('admin.user.investor.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive-lg">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($investors as $val)
                                        <tr>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ $val->mobile }}</td>
                                            <td>{{ $val->address }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>{{ $val->creator->name ?? '-' }}</td> 
                                            <td>{{ dateFormat($val->created_at) }}</td>
                                            <td>{{ dateFormat($val->updated_at) }}</td>
                                            <td>
                                                @canany(['show investor', 'edit investor', 'delete investor'])
                                                    <div class="dropdown">
                                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                            type="button" data-toggle="dropdown">Action <span
                                                                class="caret"></span></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            @can('show investor')
                                                                <li><a href="{{ route('admin.user.investor.show', $val->id) . qString() }}"><i
                                                                            class="fa fa-eye"></i> Show</a></li>
                                                            @endcan

                                                            @can('edit investor')
                                                                <li><a href="{{ route('admin.user.investor.edit', $val->id) . qString() }}"><i
                                                                            class="fa fa-pencil"></i> Edit</a></li>
                                                            @endcan
                                                            @can('delete investor')
                                                                <li><a
                                                                        onclick="deleted('{{ route('admin.user.investor.destroy', $val->id) . qString() }}')"><i
                                                                            class="fa fa-close"></i> Delete</a></li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                                @endcanany
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($investors) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $investors->appends(Request::except('page'))->links() }}
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
