@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.basic.expense-category.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Category List
                    </a>
                </li>
                @can('add expense_category')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.basic.expense-category.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Category
                        </a>
                    </li>
                @endcan
                @can('edit expense_category')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Category
                            </a>
                        </li>
                    @endif
                @endcan
                @can('show expense_category')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Category Details
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
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>{{ $data->status }}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <th>:</th>
                                    <td>{{ $data->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <th>:</th>
                                    <td>{{ $data->updater->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->updated_at) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.basic.expense-category.update', $edit) : route('admin.basic.expense-category.store') }}{{ qString() }}"
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
                        <form method="GET" action="{{ route('admin.basic.expense-category.index') }}"
                            class="form-inline">
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
                                            class="btn btn-info btn-flat"> Search </button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.basic.expense-category.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive-lg">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $val)
                                        <tr>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>{{ $val->creator->name ?? '-' }}</td>
                                            <td>{{ dateFormat($val->created_at) }}</td>
                                            <td>{{ dateFormat($val->updated_at) }}</td>
                                            <td>
                                                @canany([
                                                    'show expense_category',
                                                    'edit expense_category',
                                                    'delete
                                                    expense_category',
                                                    ])
                                                    <div class="dropdown">
                                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                            type="button" data-toggle="dropdown">Action <span
                                                                class="caret"></span></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            @can('show expense_category')
                                                                <li><a
                                                                        href="{{ route('admin.basic.expense-category.show', $val->id) . qString() }}"><i
                                                                            class="fa fa-eye"></i> Show</a></li>
                                                            @endcan
                                                            @can('edit expense_category')
                                                                <li><a
                                                                        href="{{ route('admin.basic.expense-category.edit', $val->id) . qString() }}"><i
                                                                            class="fa fa-pencil"></i> Edit</a></li>
                                                            @endcan
                                                            @can('delete expense_category')
                                                                <li><a
                                                                        onclick="deleted('{{ route('admin.basic.expense-category.destroy', $val->id) . qString() }}')"><i
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
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($categories) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $categories->appends(Request::except('page'))->links() }}
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
