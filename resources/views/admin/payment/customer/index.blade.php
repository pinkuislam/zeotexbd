@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="{{ route('admin.payment.customer-payments.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Customer Payment List
                    </a>
                </li>

                @can('add customer-payment')
                <li>
                    <a href="{{ route('admin.payment.customer-payments.receive') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Receive
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payment.customer-payments.bulk-receive') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Bulk Receive
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payment.customer-payments.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Payment
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payment.customer-payments.adjustment') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                    </a>
                </li>
                @endcan
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.payment.customer-payments.index') }}"
                        class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select class="form-control select2" name="customer">
                                        <option value="">Any Customer</option>
                                        @foreach ($customers as $item)
                                            <option value="{{ $item->id }}"
                                                {{ Request::get('customer') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select name="bank" class="form-control">
                                        <option value="">Any Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ Request::get('bank') == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select name="type" class="form-control">
                                        <option value="">Any Type</option>
                                        @foreach (['Received', 'Adjustment', 'Payment'] as $item)
                                            <option value="{{ $item }}"
                                                {{ Request::get('type') == $item ? 'selected' : '' }}>
                                                {{ $item }}</option>
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
                                    <input type="text" class="form-control" name="from" id="datepickerFrom"
                                        value="{{ dbDateRetrieve(Request::get('from')) ?? date('Y-m-d') }}" placeholder="From Date">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="to" id="datepickerTo"
                                        value="{{ dbDateRetrieve(Request::get('to')) ?? date('Y-m-d') }}" placeholder="To Date">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-custom btn-flat">Search</button>
                                    <a class="btn btn-custom btn-flat"
                                        href="{{ route('admin.payment.customer-payments.index') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Receipt No.</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Total Amount</th>
                                        <th>Customer</th>
                                        <th>Note</th>
                                        <th>Approved At</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $val)
                                        <tr>
                                            <td>{{ $val->receipt_no }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->type }}</td>
                                            <td>{{ $val->amount }}</td>
                                            <td>{{ $val->customer != null ? $val->customer->name : '' }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td>{{ dateFormat($val->approved_at) }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show customer-payment')
                                                            <li><a
                                                                    href="{{ route('admin.payment.customer-payments.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @can('edit customer-payment')
                                                            @if (!$val->approved_by && auth()->user()->id == $val->created_by)
                                                            <li><a
                                                                    href="{{ route('admin.payment.customer-payments.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-edit"></i> Edit</a></li>
                                                            @endif
                                                                @endcan
                                                                @can('delete customer-payment')
                                                                @if (!$val->approved_by && auth()->user()->id == $val->created_by)
                                                                    <li><a
                                                                            onclick="deleted('{{ route('admin.payment.customer-payments.destroy', $val->id) . qString() }}')"><i
                                                                                class="fa fa-close"></i> Delete</a></li>
                                                                @endif
                                                            @endcan

                                                            @can('approval customer-payment')
                                                            @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Branch Admin') || $val->created_by != auth()->user()->id)
                                                            @if (!$val->approved_by)
                                                                <li><a
                                                                        onclick="activity('{{ route('admin.payment.customer-payments.approve', $val->id) . qString() }}', 'Are you sure to approve this payment?')"><i
                                                                            class="fa fa-pencil"></i> Approve</a></li>
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
