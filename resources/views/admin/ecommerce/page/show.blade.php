@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.ecommerce.pages.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Page List
                </a>
            </li>

            @can('add page')
            <li>
                <a href="{{ route('admin.ecommerce.pages.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Page
                </a>
            </li>
            @endcan

            <li class="active">
                <a href="javascript:void(0);">
                    <i class="fa fa-eye" aria-hidden="true"></i> Page Details
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width:120px;">ID</th>
                                        <th style="width:10px;">:</th>
                                        <td>{{ $data->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <th>:</th>
                                        <td>{{ $data->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Details</th>
                                        <th>:</th>
                                        <td>{!! $data->details !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <th>:</th>
                                        <td>{{ $data->creator->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <th>:</th>
                                        <td>{{ dateFormat($data->created_at, 1) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated By</th>
                                        <th>:</th>
                                        <td>{{ $data->updater->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <th>:</th>
                                        <td>{{ dateFormat($data->updated_at, 1) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <h3>Image</h3>
                              {!! viewImg('pages', $data->image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:300px; height:200px;']) !!}
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
