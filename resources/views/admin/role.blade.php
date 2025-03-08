@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ (isset($list)) ? 'class=active' : '' }}>
                <a href="{{ route('admin.role.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Role List
                </a>
            </li>

            @can('add role') 
            <li {{ (isset($create)) ? 'class=active' : '' }}>
                <a href="{{ route('admin.role.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Role
                </a>
            </li>
            @endcan

            @if (isset($edit))
            <li class="active">
                <a href="#">
                    <i class="fa fa-edit" aria-hidden="true"></i> Edit Role
                </a>
            </li>
            @endif
        </ul>

        <div class="tab-content">
            @if(isset($edit) || isset($create))
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ isset($edit) ? route('admin.role.update', $edit) : route('admin.role.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        @if (isset($edit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="name" value="{{ old('name', isset($data) ? $data->name : '') }}" required>

                                        @if ($errors->has('name'))
                                            <span class="help-block">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <h4>Permissions</h4>
                                        @if ($errors->has('permissions'))
                                            <span class="help-block">{{ $errors->first('permissions') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @foreach($permissionArr as $module => $moduleArr)
                                <div class="form-group">
                                    <label class="control-label col-sm-3">{{ ucfirst($module) }}:</label>
                                    <div class="col-sm-9">
                                        @foreach($moduleArr as $md)
                                            <label style="margin-right:10px; margin-top:6px;">
                                                <input type="checkbox" name="permissions[]" value="{{ $md->name }}" {{ in_array($md->name, $rolePermissions) ?'checked' : '' }}> 
                                                {{ str_replace($module, '', $md->name) }}
                                            </label>
                                        @endforeach    
                                    </div>
                                </div>
                                @endforeach

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                    <button type="reset" class="btn btn-custom btn-flat btn-lg">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @elseif (isset($list))
            <div class="tab-pane active">
                <form method="GET" action="{{ route('admin.role.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-custom btn-flat"><i class="fa fa-search"></i></button>
                                <a class="btn btn-custom btn-flat" href="{{ route('admin.role.index') }}"><i class="fa fa-times"></i></a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $val)
                            <tr>
                                <td>{{ $val->name }}</td>
                                <td>
                                    {{ implode(', ', $val->getPermissionNames()->toArray()) }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('edit role') 
                                            <li><a href="{{ route('admin.role.edit', $val->id).qString() }}"><i class="fa fa-pencil"></i> Edit</a></li>
                                            @endcan
                                            
                                            @can('delete role') 
                                            <li><a onclick="deleted('{{ route('admin.role.destroy', $val->id).qString() }}')"><i class="fa fa-trash"></i> Delete</a></li>
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
