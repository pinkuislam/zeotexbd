@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ isset($list) ? 'class=active' : '' }}>
                <a href="{{ route('admin.basic.bank.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Bank List
                </a>
            </li>

            @can('add bank')
            <li {{ isset($create) ? 'class=active' : '' }}>
                <a href="{{ route('admin.basic.bank.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Bank
                </a>
            </li>
            @endcan

            @if (isset($edit))
            <li class="active">
                <a href="javascript:void(0);">
                    <i class="fa fa-edit" aria-hidden="true"></i> Edit Bank
                </a>
            </li>
            @endif

            @if (isset($show))
            <li class="active">
                <a href="javascript:void(0);">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> Bank Details
                </a>
            </li>
            @endif
        </ul>

        <div class="tab-content">
            @if(isset($show))
            <div class="tab-pane active">
                <div class="box-body table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:120px;">Code</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->code }}</td>
                        </tr>
                        <tr>
                            <th>Account Name</th>
                            <th>:</th>
                            <td>{{ $data->account_name }}</td>
                        </tr>
                        <tr>
                            <th>Account No</th>
                            <th>:</th>
                            <td>{{ $data->account_no }}</td>
                        </tr>
                        <tr>
                            <th>Bank Name</th>
                            <th>:</th>
                            <td>{{ $data->bank_name }}</td>
                        </tr>
                        <tr>
                            <th>Branch Name</th>
                            <th>:</th>
                            <td>{{ $data->branch_name }}</td>
                        </tr>
                        <tr>
                            <th>Opening Balance</th>
                            <th>:</th>
                            <td>{{ $data->opening_balance }}</td>
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
                    <form method="POST" action="{{ isset($edit) ? route('admin.basic.bank.update', $edit) : route('admin.basic.bank.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        @if (isset($edit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group{{ $errors->has('account_name') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Account Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account_name" value="{{ old('account_name', isset($data) ? $data->account_name : '') }}" required>

                                        @if ($errors->has('account_name'))
                                            <span class="help-block">{{ $errors->first('account_name') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('account_no') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Account No:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="account_no" value="{{ old('account_no', isset($data) ? $data->account_no : '') }}" required>

                                        @if ($errors->has('account_no'))
                                            <span class="help-block">{{ $errors->first('account_no') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('bank_name') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Bank Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name', isset($data) ? $data->bank_name : '') }}" required>

                                        @if ($errors->has('bank_name'))
                                            <span class="help-block">{{ $errors->first('bank_name') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('branch_name') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3">Branch Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="branch_name" value="{{ old('branch_name', isset($data) ? $data->branch_name : '') }}">

                                        @if ($errors->has('branch_name'))
                                            <span class="help-block">{{ $errors->first('branch_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group{{ $errors->has('opening_balance') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Opening Balance:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="opening_balance"
                                            value="{{ old('opening_balance', isset($data) ? $data->opening_balance : '') }}"
                                            required>

                                        @if ($errors->has('opening_balance'))
                                            <span class="help-block">{{ $errors->first('opening_balance') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Status:</label>
                                    <div class="col-sm-9">
                                        <select name="status" class="form-control select2" required>
                                            @php ($status = old('status', isset($data) ? $data->status : ''))
                                            @foreach(['Active', 'Deactivated'] as $sts)
                                                <option value="{{ $sts }}" {{ ($status == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('status'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('status') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
                                    <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @elseif (isset($list))
            <div class="tab-pane active">
                <form method="GET" action="{{ route('admin.basic.bank.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">Any Status</option>
                                    @foreach(['Active', 'Deactivated'] as $sts)
                                        <option value="{{ $sts }}" {{ (Request::get('status') == $sts) ? 'selected' : '' }}>{{ $sts }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-custom btn-flat">Search</button>
                                <a class="btn btn-custom btn-flat" href="{{ route('admin.basic.bank.index') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Account Name</th>
                                <th>Account No</th>
                                <th>Bank Name</th>
                                <th>Branch Name</th>
                                <th>Opening Balance</th>
                                <th>Status</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banks as $val)
                            <tr>
                                <td>{{ $val->code }}</td>
                                <td>{{ $val->account_name }}</td>
                                <td>{{ $val->account_no }}</td>
                                <td>{{ $val->bank_name }}</td>
                                <td>{{ $val->branch_name }}</td>
                                <td>{{ $val->opening_balance }}</td>
                                <td>{{ $val->status }}</td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                            type="button" data-toggle="dropdown">Action <span
                                                class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('show bank')
                                            <li>
                                                <a href="{{ route('admin.basic.bank.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a>
                                            </li>
                                            @endcan
                                            
                                            @can('edit bank')
                                            <li>
                                                <a href="{{ route('admin.basic.bank.edit', $val->id) . qString() }}"><i class="fa fa-pencil"></i> Edit</a>
                                            </li>
                                            @endcan
                                                        
                                            @can('status bank')
                                            <li><a onclick="activity('{{ route('admin.basic.bank.status', $val->id) . qString() }}')"><i class="fa fa-toggle-off"></i> {{ $val->status == 'Active' ? 'Deactivated' : 'Active' }}</a></li>
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
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($banks) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $banks->appends(Request::except('page'))->links() }}
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