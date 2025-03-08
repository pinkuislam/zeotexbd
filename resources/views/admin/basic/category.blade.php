@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.basic.category.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Category List
                    </a>
                </li>

                @can('add category')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.basic.category.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Category
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-upload" aria-hidden="true"></i> Import Category
                        </a>
                    </li>
                @endcan

                <li>
                    <a href="{{ route('admin.basic.category.export') . qString() }}">
                        <i class="fa fa-download" aria-hidden="true"></i> Export Category
                    </a>
                </li>

                @if (isset($edit))
                    <li class="active">
                        <a href="javascript:void(0);">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Category
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="javascript:void(0);">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Category Details
                        </a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8 col-sm-6">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width:150px;">Name</th>
                                                <th style="width:10px;">:</th>
                                                <td>{{ $data->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Image</th>
                                                <th>:</th>
                                                <td>{!! viewImg('categories', $data->image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}</td>
                                            </tr>
                                            <tr>
                                                <th>Parent Category</th>
                                                <th>:</th>
                                                @if (config('settings.category_layer') > 1)
                                                <td>{{ $data->parent->name ?? '-' }}</td>
                                                @endif
                                            </tr>
                                            <tr>
                                                <th>Created By</th>
                                                <th>:</th>
                                                <td>{{ isset($data->creator) ? $data->creator->name : '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Updated By</th>
                                                <th>:</th>
                                                <td>{{ isset($data->updater) ? $data->updater->name : '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <th>:</th>
                                                <td>{{ $data->status }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.basic.category.update', $edit) : route('admin.basic.category.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            @if ($categories)
                                            <div class="form-group @error('parent_id') has-error @enderror">
                                                <label class="control-label col-sm-3">Parent Category:</label>
                                                <div class="col-sm-9">
                                                    <x-category-dropdown 
                                                        :categories="$categories" 
                                                        :value="old('parent_id', isset($data) ? $data->parent_id : '')"
                                                        field="parent_id"
                                                        :required="false"
                                                        :lastitem="false"
                                                    />
                                    
                                                    @error('parent_id')
                                                        <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            @endif
                                    
                                            <div class="form-group @error('name') has-error @enderror">
                                                <label class="control-label col-sm-3 required">Name:</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="name" value="{{ old('name', isset($data) ? $data->name : '') }}" required>
                                    
                                                    @error('name')
                                                        <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                    
                                            <div class="form-group @error('image') has-error @enderror">
                                                <label class="control-label col-sm-3 required">Image:</label>
                                                <div class="col-sm-9">
                                                <x-sp-components::image-input id="image" name="image" path="{{ isset($data) ? MediaUploader::showUrl('categories', $data->image) : null }}" />
                                                </div>
                                            </div>
                                            <div class="form-group @error('status') has-error @enderror">
                                                <label class="control-label col-sm-3 required">Status:</label>
                                                <div class="col-sm-9">
                                                    <select name="status" class="form-control select2" required>
                                                        @php ($status = old('status', isset($data) ? $data->status : ''))
                                                        @foreach(['Active', 'Deactivated'] as $sts)
                                                            <option value="{{ $sts }}" {{ ($status == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
                                                        @endforeach
                                                    </select>
                                    
                                                    @error('status')
                                                        <span class="help-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                    
                                            <div class="form-group @error('seo_box') has-error @enderror">
                                                <label class="control-label col-sm-3 required">SEO BOX:</label>
                                                <div class="col-sm-9">
                                                    <x-sp-components::seo-meta-input title_field="meta_title" keywords_field="meta_keywords" description_field="meta_description" :data="$data ?? null"/>
                                                </div>
                                            </div>
                                    
                                            <div class="form-group text-center">
                                                <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
                                                <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.basic.category.index') }}" class="form-inline">
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
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.basic.category.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Parent Category</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $val)
                                        <tr>
                                            <td>{!! viewImg('categories', $val->image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}</td>
                                            <td>{{ $val->name }}</td>
                                            @if (config('settings.category_layer') > 1)
                                            <td>{{ $val->parent->name ?? '-' }}</td>
                                            @endif
                                            <td>{{ $val->creator->name ?? '-' }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">

                                                        @can('show category')
                                                            <li><a
                                                                    href="{{ route('admin.basic.category.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                     

                                                        @can('edit category')
                                                            <li><a
                                                                    href="{{ route('admin.basic.category.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan
                                                        @can('delete category') 
                                                        <li>
                                                            <a href="javascript:void(0);" onclick="deleted('{{ route('admin.basic.category.destroy', $val->id).qString() }}')">
                                                                <i class="fa fa-close"></i> Delete
                                                            </a>
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
            <form method="POST" action="{{ route('admin.basic.category.import') . qString() }}"
                enctype="multipart/form-data" class="non-validate">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Import Category</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            Example Excel file download <a href="{{ asset('excels/categories.xlsx') }}">click here</a>
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
