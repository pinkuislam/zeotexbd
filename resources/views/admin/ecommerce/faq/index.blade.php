@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="{{ route('admin.ecommerce.faq.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Faq List
                </a>
            </li>

            @can('add faq')
            <li>
                <a href="{{ route('admin.ecommerce.faq.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Faq
                </a>
            </li>
            @endcan
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <form method="GET" action="{{ route('admin.ecommerce.faq.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">From</span>
                                    <input type="text" class="form-control" id="datepickerFrom" name="from" value="{{ Request::get('from') }}">
                                    <span class="input-group-addon">To</span>
                                    <input type="text" class="form-control" id="datepickerTo" name="to" value="{{ Request::get('to') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                            </div>

                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">Any Status</option>
                                    @foreach(['Active', 'Deactivated'] as $sts)
                                        <option value="{{ $sts }}" {{ (Request::get('status') == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-custom btn-flat">Search</button>
                                <a class="btn btn-custom btn-flat" href="{{ route('admin.ecommerce.faq.index') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th class="col-action">SL</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $val)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $val->question }}</td>
                                <td>{{ $val->answer }}</td>
                                <td>{{ $val->creator->name ?? '-' }}</td>
                                <td>{{ dateFormat($val->created_at, 1) }}</td>
                                <td>{{ $val->status }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                            type="button" data-toggle="dropdown">Action <span
                                                class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">

                                            @can('show slider')
                                                <li><a
                                                        href="{{ route('admin.ecommerce.faq.show', $val->id) . qString() }}"><i
                                                            class="fa fa-eye"></i> Show</a></li>
                                            @endcan
                                         

                                            @can('edit slider')
                                                <li><a
                                                        href="{{ route('admin.ecommerce.faq.edit', $val->id) . qString() }}"><i
                                                            class="fa fa-pencil"></i> Edit</a></li>
                                            @endcan
                                            @can('delete slider') 
                                            <li>
                                                <a href="javascript:void(0);" onclick="deleted('{{ route('admin.ecommerce.faq.destroy', $val->id).qString() }}')">
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
        </div>
    </div>
</section>
@endsection
