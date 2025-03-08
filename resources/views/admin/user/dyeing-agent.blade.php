@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.user.dyeing-agent.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Dyeing Agent List
                    </a>
                </li>

                @can('add supplier')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.user.dyeing-agent.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Dyeing Agent
                        </a>
                    </li>
                @endcan

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Dyeing Agent
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Dyeing Agent Details
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
                                    <th style="width:120px;">Code</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->code }}</td>
                                </tr>
                                <tr>
                                    <th style="width:120px;">Name</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->name }}</td>
                                </tr>
                                <tr>
                                    <th style="width:120px;">Email</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->email ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Person</th>
                                    <th>:</th>
                                    <td>{{ $data->contact_person }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <th>:</th>
                                    <td>{{ $data->contact_no }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <th>:</th>
                                    <td>{{ $data->address }}</td>
                                </tr>
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
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.user.dyeing-agent.update', $edit) : route('admin.user.dyeing-agent.store') }}{{ qString() }}"
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
                                                    value="{{ old('code', isset($data) ? $data->code : '') }}" readonly>

                                                @if ($errors->has('code'))
                                                    <span class="help-block">{{ $errors->first('code') }}</span>
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

                                        <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 ">Contact Person:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="contact_person"
                                                    value="{{ old('contact_person', isset($data) ? $data->contact_person : '') }}">

                                                @if ($errors->has('contact_person'))
                                                    <span class="help-block">{{ $errors->first('contact_person') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('contact_no') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Contact Number:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="contact_no"
                                                    value="{{ old('contact_no', isset($data) ? $data->contact_no : '') }}"
                                                    required>

                                                @if ($errors->has('contact_no'))
                                                    <span class="help-block">{{ $errors->first('contact_no') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            <label
                                                class="control-label col-sm-3 {{ isset($create) ? '' : '' }}">Email:</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ old('email', isset($data) ? $data->email : '') }}">

                                                @if ($errors->has('email'))
                                                    <span class="help-block">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Address:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="address"
                                                    value="{{ old('address', isset($data) ? $data->address : '') }}">

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
                                                    @foreach (['Active', 'Deactivated'] as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('status'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('status') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-3 text-center">
                                                <button type="submit"
                                                    class="btn btn-success btn-flat">{{ isset($edit) ? __('Update') : __('Create') }}</button>
                                                <button type="reset"
                                                    class="btn btn-warning btn-flat">{{ __('Clear') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.user.dyeing-agent.index') }}" class="form-inline">
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
                                        <button type="submit" class="btn btn-info btn-flat">{{ __('Search') }}</button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.user.dyeing-agent.index') }}">X</a>
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
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Contact Person</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ $val->contact_no }}</td>
                                            <td>{{ $val->email }}</td>
                                            <td>{{ $val->contact_person }}</td>
                                            <td>{{ $val->address }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show supplier')
                                                            <li><a
                                                                    href="{{ route('admin.user.dyeing-agent.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @can('edit supplier')
                                                            <li><a
                                                                    href="{{ route('admin.user.dyeing-agent.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan

                                                        @can('delete supplier')
                                                            <li><a
                                                                    onclick="activity('{{ route('admin.user.dyeing-agent.status', $val->id) . qString() }}')"><i
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
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($records) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $records->appends(Request::except('page'))->links() }}
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
