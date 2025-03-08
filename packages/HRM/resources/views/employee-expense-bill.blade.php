@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee-expense-bill.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Employee Expense List
                    </a>
                </li>
                @can('add hr_employee-expense-bill')
                <li {{ isset($create) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee-expense-bill.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Employee Expense
                    </a>
                </li>
                @endcan

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Employee Expense
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Employee Expense Details
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
                                    <th>DA Amount</th>
                                    <th>:</th>
                                    <td>{{ $data->da_amount }}</td>
                                </tr>
                                <tr>
                                    <th>TA Amount</th>
                                    <th>:</th>
                                    <td>{{ $data->ta_amount }}</td>
                                </tr>
                                <tr>
                                    <th>Hotel Bill</th>
                                    <th>:</th>
                                    <td>{{ $data->hotel_bill }}</td>
                                </tr>
                                <tr>
                                    <th>Total Amount</th>
                                    <th>:</th>
                                    <td>{{ $data->total_amount }}</td>
                                </tr>
                                <tr>
                                    <th>Daily Summary</th>
                                    <th>:</th>
                                    <td>{{ $data->daily_summary }}</td>
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
                                action="{{ isset($edit) ? route('oshnisoft-hrm.employee-expense-bill.update', $edit) : route('oshnisoft-hrm.employee-expense-bill.store') }}{{ qString() }}"
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

                                        <div class="form-group{{ $errors->has('da_amount') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-3">DA Amount:</label>
                                            <div class="col-sm-9">
                                                <input type="number" name="da_amount" class="form-control" value="{{ old('da_amount', isset($data) ? $data->da_amount : '') }}" required>
                                                @if ($errors->has('da_amount'))
                                                    <span class="help-block">{{ $errors->first('da_amount') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('ta_amount') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-3">TA Amount:</label>
                                            <div class="col-sm-9">
                                                <input type="number" name="ta_amount" class="form-control" value="{{ old('ta_amount', isset($data) ? $data->ta_amount : '') }}" required>
                                                @if ($errors->has('ta_amount'))
                                                    <span class="help-block">{{ $errors->first('ta_amount') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('hotel_bill') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-3">Hotel Bill:</label>
                                            <div class="col-sm-9">
                                                <input type="number" name="hotel_bill" class="form-control" value="{{ old('hotel_bill', isset($data) ? $data->hotel_bill : '') }}" required>
                                                @if ($errors->has('hotel_bill'))
                                                    <span class="help-block">{{ $errors->first('hotel_bill') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('daily_summary') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Daily Summary:</label>
                                            <div class="col-sm-9">
                                                <textarea name="daily_summary" class="form-control" rows="3">{{ old('daily_summary', isset($data) ? $data->daily_summary : '') }}</textarea>
                                                @if ($errors->has('daily_summary'))
                                                    <span class="help-block">{{ $errors->first('daily_summary') }}</span>
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
                        <form method="GET" action="{{ route('oshnisoft-hrm.employee-expense-bill.index') }}" class="form-inline">
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
                                        <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.employee-expense-bill.index') }}">X</a>
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
                                        <th>DA Amount</th>
                                        <th>TA Amount</th>
                                        <th>Hotel Bill</th>
                                        <th>Total Amount</th>
                                        <th>Is Holiday</th>
                                        <th>Daily Summary</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee_expense_bills as $val)
                                        <tr>
                                            <td>{{ $val->employee->employee_no }}</td>
                                            <td>{{ $val->employee->name }}</td>
                                            <td>{{ $val->date }}</td>
                                            <td>{{ $val->da_amount }}</td>
                                            <td>{{ $val->ta_amount }}</td>
                                            <td>{{ $val->hotel_bill }}</td>
                                            <td>{{ $val->total_amount }}</td>
                                            <td>{{ $val->is_holiday }}</td>
                                            <td>{{ $val->daily_summary }}</td>
                                            <td>
                                                {{ $val->status }}
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button"
                                                        data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show hr_employee-expense-bill')
                                                        <li><a href="{{ route('oshnisoft-hrm.employee-expense-bill.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                        @if($val->status == 'Pending')
                                                            @can('edit hr_employee-expense-bill')
                                                                <li><a href="{{ route('oshnisoft-hrm.employee-expense-bill.edit', $val->id) . qString() }}"><i class="fa fa-eye"></i> Edit</a></li>
                                                            @endcan

                                                            @can('delete hr_employee-expense-bill')
                                                                <li><a onclick="deleted('{{ route('oshnisoft-hrm.employee-expense-bill.destroy', $val->id) . qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                                            @endcan
                                                            @can('approve hr_employee-expense-bill')
                                                                <li><a onclick="activity('{{ route('oshnisoft-hrm.employee-expense-bill.approve', $val->id) . qString() }}', 'Are you sure to approve expense bill?')"><i class="fa fa-pencil"></i> Approve</a></li>
                                                            @endcan
                                                            @can('cancel hr_employee-expense-bill')
                                                                <li><a onclick="activity('{{ route('oshnisoft-hrm.employee-expense-bill.reject', $val->id) . qString() }}', 'Are you sure to cancel expense bill?')"><i class="fa fa-close"></i> Cancel</a></li>
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
