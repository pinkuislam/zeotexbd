@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="{{ route('admin.payment.fund-transfers.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Fund Transfer List
                    </a>
                </li>

                @can('add fund-transfer')
                <li>
                    <a href="{{ route('admin.payment.fund-transfers.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Fund Transfer
                    </a>
                </li>
                @endcan
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.payment.fund-transfers.index') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select name="bank" class="form-control">
                                        <option value="">Any Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ Request::get('bank') == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">All</option>
                                        @foreach (['Approved', 'Pending'] as $item)
                                            <option value="{{ $item }}"
                                                {{ Request::get('status') == $item ? 'selected' : '' }}>
                                                {{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="from" id="datepickerFrom" value="{{ dbDateRetrieve(Request::get('from')) ?? date('Y-m-d') }}" placeholder="From Date">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="to" id="datepickerTo" value="{{ dbDateRetrieve(Request::get('to')) ?? date('Y-m-d') }}" placeholder="To Date">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.payment.fund-transfers.index') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Bank (From)</th>
                                        <th>Bank (To)</th>
                                        <th>Date</th>
                                        <th>Note</th>
                                        <th>Amount</th>
                                        <th>Approved At</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $val)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.payment.fund-transfers.show', $val->id) . qString() }}">{{ isset($val->fromBank) ? $val->fromBank->bank_name . '[' . $val->fromBank->account_no . ']' : '' }}</a>
                                            </td>
                                            <td>{{ $val->toBank != null ? $val->toBank->bank_name . '[' . $val->toBank->account_no . ']' : '-' }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td>{{ number_format($val->amount, 2) }}</td>
                                            <td>{{ dateFormat($val->approved_at) }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show fund-transfer')
                                                            <li><a href="{{ route('admin.payment.fund-transfers.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @can('edit fund-transfer')
                                                        @if (!$val->approved_by && auth()->user()->id == $val->created_by)
                                                            <li><a href="{{ route('admin.payment.fund-transfers.edit', $val->id) . qString() }}"><i class="fa fa-edit"></i> Edit</a></li>
                                                        @endif
                                                        @endcan

                                                        @can('delete fund-transfer')
                                                        @if (!$val->approved_by && auth()->user()->id == $val->created_by)
                                                            <li><a onclick="deleted('{{ route('admin.payment.fund-transfers.destroy', $val->id) . qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                                        @endif
                                                        @endcan

                                                        @can('approval fund-transfer')
                                                            @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Branch Admin') || $val->created_by != auth()->user()->id)
                                                            @if (!$val->approved_by )
                                                                <li><a onclick="activity('{{ route('admin.payment.fund-transfers.approve', $val->id) . qString() }}', 'Are you sure to approve this transfer?')"><i class="fa fa-pencil"></i> Approve</a></li>
                                                            @endif
                                                            @endif
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
