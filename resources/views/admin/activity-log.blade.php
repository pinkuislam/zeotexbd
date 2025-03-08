@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Activity Log
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.report.bank') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn-flat">{{ __('Search') }}</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.report.bank') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Log Name</th>
                                    <th>Description</th>
                                    <th>Event</th>
                                    <th>Attributes</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $val)
                                    <tr>
                                        <td>
                                            @php
                                                $user = App\Models\User::find($val->causer_id);
                                            @endphp
                                            {{ $user->name }}
                                        </td>
                                        <td>{{ $val->log_name }}</td>
                                        <td>{{ $val->description }}</td>
                                        <td>{{ $val->event }}</td>
                                        <td>
                                            @foreach ($val->properties['attributes'] as $k => $v)
                                                @if ($v != '')
                                                    <p><?= '<b>' . ucwords(str_replace('_', ' ', $k)) . '</b> = ' . $v ?>
                                                    </p>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>{{ $val->created_at->diffForHumans() }}</td>
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
