@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li {{ (isset($list)) ? 'class=active' : '' }}>
                <a href="{{ route('admin.payment.invest.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Invest List
                </a>
            </li>

            @can('add invest')
            <li {{ (isset($create)) ? 'class=active' : '' }}>
                <a href="{{ route('admin.payment.invest.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Invest
                </a>
            </li>
            @endcan

            @can('edit invest')
            @if (isset($edit))
            <li class="active">
                <a href="#">
                    <i class="fa fa-edit" aria-hidden="true"></i> Edit Invest
                </a>
            </li>
            @endif
            @endcan

            @can('show invest')
            @if (isset($show))
            <li class="active">
                <a href="#">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> Invest  Details
                </a>
            </li>
            @endif
            @endcan
        </ul>

        <div class="tab-content">
            @if(isset($show))
            <div class="tab-pane active">
                <div class="box-body table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:120px;">Investor</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->investor != null ? $data->investor->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Bank</th>
                            <th>:</th>
                            <td>{{ $data->bank != null ? $data->bank->bank_name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Note</th>
                            <th>:</th>
                            <td>{!! nl2br($data->note) !!}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th>:</th>
                            <td>{{ $data->amount }}</td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <th>:</th>
                            <td>{{ $data->creator->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Updated By</th>
                            <th>:</th>
                            <td>{{ $data->updater->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @elseif(isset($edit) || isset($create))
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ isset($edit) ? route('admin.payment.invest.update', $edit) : route('admin.payment.invest.store') }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf

                        @if (isset($edit))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group{{ $errors->has('investor_id') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Investor:</label>
                                    <div class="col-sm-9">
                                        <select name="investor_id" class="form-control select2" required>
                                            <option value="">Select Investor</option>
                                            @php ($investor_id = old('investor_id', isset($data) ? $data->investor_id : ''))
                                            @foreach($investors as $investor)
                                                <option value="{{ $investor->id }}" {{ ($investor_id == $investor->id) ? 'selected' : '' }}>{{ $investor->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('investor_id'))
                                            <span class="help-block">{{ $errors->first('investor_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('bank_id') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Bank:</label>
                                    <div class="col-sm-9">
                                        <select name="bank_id" class="form-control select2" required>
                                            <option value="">Select Bank</option>
                                            @php ($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ ($bank_id == $bank->id) ? 'selected' : '' }}>{{ $bank->bank_name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('bank_id'))
                                            <span class="help-block">{{ $errors->first('bank_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Date:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control datepicker" name="date" value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : '') }}" required>

                                        @if ($errors->has('date'))
                                            <span class="help-block">{{ $errors->first('date') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3">Note :</label>
                                    <div class="col-sm-9">
                                        <textarea type="text" class="form-control" name="note" rows="4">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
                                        @if ($errors->has('note'))
                                            <span class="help-block">{{ $errors->first('note') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3 required">Amount:</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="any" min="0" class="form-control" name="amount" value="{{ old('amount', isset($data) ? $data->amount : '') }}" required>

                                        @if ($errors->has('amount'))
                                            <span class="help-block">{{ $errors->first('amount') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-3 text-center">
                                        <button type="submit" class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                        <button type="reset" class="btn btn-warning btn-flat"> Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @elseif (isset($list))
            <div class="tab-pane active">
                <form method="GET" action="{{ route('admin.payment.invest.index') }}" class="form-inline">
                    <div class="box-header text-right">
                        <div class="row">
                            <div class="form-group">
                                <select name="investor" class="form-control select2">
                                    <option value="">Any Investor</option>
                                    @foreach($investors as $investor)
                                        <option value="{{ $investor->id }}" {{ (Request::get('investor') == $investor->id) ? 'selected' : '' }}>{{ $investor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="bank" class="form-control select2">
                                    <option value="">Any Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ (Request::get('bank') == $bank->id) ? 'selected' : '' }}>{{ $bank->bank_name }}</option>
                                    @endforeach
                                </select>
                            </div>                            
                            <div class="form-group">
                                <input type="text" class="form-control" name="from" id="datepickerFrom" value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="to" id="datepickerTo" value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-info btn-flat">Search</button>
                                <a class="btn btn-warning btn-flat" href="{{ route('admin.payment.invest.index') }}">X</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="box-body table-responsive-lg">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Investor</th>
                                <th>Bank</th>
                                <th>Date</th>
                                <th>Note</th>
                                <th>Amount</th>
                                <th>Created By</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invests as $val)
                            <tr>
                                <td>{{ $val->investor != null ? $val->investor->name : '-' }}</td>
                                <td>{{ $val->bank != null ? $val->bank->bank_name : '-' }}</td>
                                <td>{{ dateFormat($val->date) }}</td>
                                <td>{{ $val->note }}</td>
                                <td>{{ $val->amount }}</td>
                                <td>{{ $val->creator->name ?? '-' }}</td>
                                <td>
                                    @canany(['show invest', 'edit invest', 'delete invest'])
                                    <div class="dropdown">
                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @can('show invest')
                                            <li><a href="{{ route('admin.payment.invest.show', $val->id).qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                            @endcan
                                            @can('edit invest')
                                            <li><a href="{{ route('admin.payment.invest.edit', $val->id).qString() }}"><i class="fa fa-pencil"></i> Edit</a></li>
                                            @endcan
                                            @can('delete invest')
                                            <li><a onclick="deleted('{{ route('admin.payment.invest.destroy', $val->id).qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
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
                    <div class="col-sm-4 pagi-msg">{!! pagiMsg($invests) !!}</div>

                    <div class="col-sm-4 text-center">
                        {{ $invests->appends(Request::except('page'))->links() }}
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
