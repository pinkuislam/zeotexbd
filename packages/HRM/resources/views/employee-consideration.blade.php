@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee-consideration.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Employee Incentive List
                    </a>
                </li>
                @can('add hr_employee-consideration')
                <li {{ isset($create) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee-consideration.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Employee Consideration
                    </a>
                </li>
                @endcan

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Employee Consideration
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Employee Consideration Details
                        </a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:120px;">Staff ID</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->employee->employee_no }}</td>
                                </tr>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>:</th>
                                    <td>{{ $data->employee->name }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ $data->date }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <th>:</th>
                                    <td>{{ $data->amount }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{{ $data->note }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>{{ $data->status }}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <th>:</th>
                                    <td>{{ isset($data->createdBy) ?  $data->createdBy->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <th>:</th>
                                    <td>{{ isset($data->updatedBy) ?  $data->updatedBy->name : '' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('oshnisoft-hrm.employee-consideration.update', $edit) : route('oshnisoft-hrm.employee-consideration.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">

                                        <div class="form-group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Employee:</label>
                                            <div class="col-sm-9">
                                                @php($employee_id = old('employee_id', isset($data) ? $data->employee_id : ''))
                                                <select name="employee_id" class="form-control">
                                                    <option value="">Select Employee</option>
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}"
                                                            {{ $employee_id == $employee->id ? 'selected' : '' }}>
                                                            {{ $employee->name . ' [' . $employee->employee_no . ']' }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('employee_id'))
                                                    <span
                                                        class="help-block">{{ $errors->first('employee_id') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ old('date', isset($data) ? $data->date : date('Y-m-d')) }}"
                                                    required>

                                                @if ($errors->has('date'))
                                                    <span
                                                        class="help-block">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-3">Amount:</label>
                                            <div class="col-sm-9">
                                                <input type="number" name="amount" class="form-control" value="{{ old('amount', isset($data) ? $data->amount : '') }}" required>
                                                @if ($errors->has('amount'))
                                                    <span class="help-block">{{ $errors->first('amount') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Note:</label>
                                            <div class="col-sm-9">
                                                <textarea name="note" class="form-control" rows="3">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
                                                @if ($errors->has('note'))
                                                    <span class="help-block">{{ $errors->first('note') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-3 text-center">
                                                <button type="submit"
                                                    class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                                <button type="reset"
                                                    class="btn btn-warning btn-flat">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('oshnisoft-hrm.employee-consideration.index') }}" class="form-inline">
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
                                        <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.employee-consideration.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Emp. Name</th>
                                        <th>Date</th>
                                        <th>Note</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee_consideration as $val)
                                        <tr>
                                            <td>{{ $val->employee->employee_no }}</td>
                                            <td>{{ $val->employee->name }}</td>
                                            <td>{{ $val->date }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td>{{ $val->amount }}</td>
                                            <td>
                                                {{ $val->status }}
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button"
                                                        data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show hr_employee-consideration')
                                                        <li><a href="{{ route('oshnisoft-hrm.employee-consideration.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @if($val->status == 'Pending')
                                                            @can('edit hr_employee-consideration')
                                                                <li><a href="{{ route('oshnisoft-hrm.employee-consideration.edit', $val->id) . qString() }}"><i class="fa fa-eye"></i> Edit</a></li>
                                                            @endcan

                                                            @can('delete hr_employee-consideration')
                                                                <li><a onclick="deleted('{{ route('oshnisoft-hrm.employee-consideration.destroy', $val->id) . qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                                            @endcan
                                                            @can('approve hr_employee-consideration')
                                                                <li><a onclick="activity('{{ route('oshnisoft-hrm.employee-consideration.approve', $val->id) . qString() }}', 'Are you sure to approve consideration?')"><i class="fa fa-pencil"></i> Approve</a></li>
                                                            @endcan
                                                            @can('cancel hr_employee-consideration')
                                                                <li><a onclick="activity('{{ route('oshnisoft-hrm.employee-consideration.reject', $val->id) . qString() }}', 'Are you sure to cancel consideration?')"><i class="fa fa-close"></i> Cancel</a></li>
                                                            @endcan
                                                        @endif


                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
