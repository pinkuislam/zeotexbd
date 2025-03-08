@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="{{ route('admin.payment.loan-payments.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Loan Payment List
                    </a>
                </li>

                @can('add loan')
                    <li>
                        <a href="{{ route('admin.payment.loan-payments.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Loan Payment
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.payment.loan-payments.adjustment') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                        </a>
                    </li>
                @endcan
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.payment.loan-payments.index') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select class="form-control select2" name="loanHolder">
                                        <option value="0">Any Loan Holder</option>
                                        @foreach ($loanHolders as $item)
                                            <option value="{{ $item->id }}" {{ Request::get('loanHolder') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control select2" name="bank">
                                        <option value="0">Any Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ Request::get('bank') == $bank->id ? 'selected' : '' }}>{{ $bank->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control select2" name="type">
                                        <option value="0">Any Type</option>
                                        @foreach (['Received', 'Adjustment', 'Payment'] as $item)
                                            <option value="{{ $item }}" {{ Request::get('type') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>

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
                                    <button type="submit" class="btn btn-custom btn-flat">Search</button>
                                    <a class="btn btn-custom btn-flat" href="{{ route('admin.payment.loan-payments.index') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive-lg">
                        <table class="table table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Loan Holder</th>
                                    <th>Amount</th>
                                    <th>Created By</th>
                                    <th class="col-action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $val)
                                    <tr>
                                        <td>{{ dateFormat($val->date) }}</td>
                                        <td>{{ $val->type }}</td>
                                        <td>{{ $val->loanHolder->name ?? '-'  }}</td>
                                        <td>{{ $val->amount }}</td>
                                        <td>{{ $val->creator->name ?? '-' }}</td>
                                        <td>
                                            <x-sp-components::action-group>
                                                @can('show loan')
                                                <li>
                                                    <a href="{{ route('admin.payment.loan-payments.show', $val->id) . qString() }}">
                                                        <i class="fa fa-eye"></i> Show
                                                    </a>
                                                </li>
                                                @endcan

                                                @if ($val->actionable_id == null)
                                                    @can('edit loan')
                                                    <li>
                                                        <a href="{{ route('admin.payment.loan-payments.edit', $val->id) . qString() }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    @can('delete loan')
                                                    <li>
                                                        <a onclick="deleted('{{ route('admin.payment.loan-payments.destroy', $val->id) . qString() }}')">
                                                            <i class="fa fa-close"></i> Delete
                                                        </a>
                                                    </li>
                                                    @endcan
                                                @endif
                                            </x-sp-components::action-group>
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
            </div>
        </div>
    </section>
@endsection
