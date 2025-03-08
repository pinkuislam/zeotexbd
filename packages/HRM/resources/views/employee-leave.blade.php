@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee-leave.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Employee Leave List
                    </a>
                </li>
                @can('add hr_employee-leave')
                <li {{ isset($create) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee-leave.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Employee Leave
                    </a>
                </li>
                @endcan

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Employee Leave
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Employee Leave Details
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
                                    <td>{{ $data->employee->employee_no ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>:</th>
                                    <td>{{ $data->employee->name ?? ''}}</td>
                                </tr>
                                <tr>
                                    <th>Application Date</th>
                                    <th>:</th>
                                    <td>{{ $data->application_date }}</td>
                                </tr>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>:</th>
                                    <td>{{ $data->leaveType->name }}</td>
                                </tr>
                                <tr>
                                    <th>Purpose</th>
                                    <th>:</th>
                                    <td>{{ $data->purpose }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <th>:</th>
                                    <td>{{ $data->contact_number }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <th>:</th>
                                    <td>{{ $data->start_date }}</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <th>:</th>
                                    <td>{{ $data->end_date }}</td>
                                </tr>
                                <tr>
                                    <th>Attachment</th>
                                    <th>:</th>
                                    <td>
                                        @if(isset($data->attachment))
                                        {!! viewImg('leaves', $data->attachment, ['popup' => 1, 'style' => 'width:100px; height:100px;']) !!}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>{{ 'Yearly Allotted ' . $data->leaveType->name ?? '' . ' Leaves' }} </th>
                                    <th>:</th>
                                    <td>{{ $data->leaveType->day_count ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ $data->leaveType->name ?? '' . ' Leaves Enjoyed' }}</th>
                                    <th>:</th>
                                    <td>{{ $previousLeaves }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>
                                        @if($data->approved_by != null)
                                            {{ 'Approved [' . $data->approvedBy->name ?? '' . ']'  }}
                                        @elseif ($data->authorized_by != null)
                                            {{ 'Authorized [' . $data->authorizedBy->name ?? '' . ']'  }}
                                        @else
                                            {{ 'Pending' }}
                                        @endif
                                    </td>
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

                            @if($data->authorized_by == null)
                                @can('authorize hr_employee-leave')
                                    <a class="btn btn-success btn-flat" onclick="activity('{{ route('oshnisoft-hrm.employee-leave.authorize', $data->id) . qString() }}', 'Are you sure to authorize leave?')"> Authorize</a>
                                @endcan
                            @elseif ($data->approved_by == null)
                                @can('approve hr_employee-leave')
                                    <a class="btn btn-success btn-flat" onclick="activity('{{ route('oshnisoft-hrm.employee-leave.approve', $data->id) . qString() }}', 'Are you sure to approve leave?')"> Approve</a>
                                @endcan

                            @endif
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('oshnisoft-hrm.employee-leave.update', $edit) : route('oshnisoft-hrm.employee-leave.store') }}{{ qString() }}"
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
                                                <select name="employee_id" id="employee_id" class="form-control" onchange="getEmployeeLeaves()">
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
                                        <div class="form-group{{ $errors->has('leave_type_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Leave Type:</label>
                                            <div class="col-sm-9">
                                                @php($leave_type_id = old('leave_type_id', isset($data) ? $data->leave_type_id : ''))
                                                <select name="leave_type_id" class="form-control">
                                                    <option value="">Select Leave Type</option>
                                                    @foreach ($leaveTypes as $leaveType)
                                                        <option value="{{ $leaveType->id }}"
                                                            {{ $leave_type_id == $leaveType->id ? 'selected' : '' }}>
                                                            {{ $leaveType->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('leave_type_id'))
                                                    <span
                                                        class="help-block">{{ $errors->first('leave_type_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('application_date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Application Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="application_date"
                                                    value="{{ old('application_date', isset($data) ? $data->application_date : date('d-m-Y')) }}"
                                                    required>

                                                @if ($errors->has('application_date'))
                                                    <span
                                                        class="help-block">{{ $errors->first('application_date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('contact_number') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Contact Number :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="contact_number"
                                                    value="{{ old('contact_number', isset($data) ? $data->contact_number : '') }}"
                                                >

                                                @if ($errors->has('contact_number'))
                                                    <span
                                                        class="help-block">{{ $errors->first('contact_number') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('purpose') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-3">Purpose:</label>
                                            <div class="col-sm-9">
                                                <textarea name="purpose" class="form-control" rows="3" required>{{ old('purpose', isset($data) ? $data->purpose : '') }}</textarea>
                                                @if ($errors->has('purpose'))
                                                    <span class="help-block">{{ $errors->first('purpose') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Start Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="start_date"
                                                    value="{{ old('start_date', isset($data) ? $data->start_date : date('d-m-Y')) }}"
                                                    required>

                                                @if ($errors->has('start_date'))
                                                    <span
                                                        class="help-block">{{ $errors->first('start_date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">End Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="end_date"
                                                    value="{{ old('end_date', isset($data) ? $data->end_date : date('d-m-Y')) }}"
                                                    required>

                                                @if ($errors->has('end_date'))
                                                    <span class="help-block">{{ $errors->first('end_date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @canany(['approve hr_employee-leave', 'authorize hr_employee-leave'])
                                        <div class="form-group{{ $errors->has('pay_type') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Pay Type:</label>
                                            <div class="col-sm-9">
                                                @php($pay_type = old('pay_type', isset($data) ? $data->pay_type : ''))
                                                <select name="pay_type" class="form-control">
                                                    @foreach (['Paid', 'Unpaid'] as $pType)
                                                        <option value="{{ $pType }}"
                                                            {{ $pay_type == $pType ? 'selected' : '' }}>
                                                            {{ $pType }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('pay_type'))
                                                    <span
                                                        class="help-block">{{ $errors->first('pay_type') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Status:</label>
                                            <div class="col-sm-9">
                                                <select name="status" class="form-control select2" required>
                                                    @php($status = old('status', isset($data) ? $data->status : ''))
                                                    @foreach (['Pending', 'Authorized', 'Approved'] as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('status'))
                                                    <span class="help-block">{{ $errors->first('status') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endcanany

                                        <div class="form-group{{ $errors->has('attachment') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Attachment :</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="attachment">
                                                @if(isset($data->attachment))
                                                <img src="{{ asset('storage/leaves/thumb/'.$data->attachment) }}" alt="{{ $data->attachment }}" class="img-thumbnail">
                                                @endif
                                                @if ($errors->has('attachment'))
                                                    <span class="help-block">{{ $errors->first('attachment') }}</span>
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

                                    <div class="col-sm-4" id="leaveDiv">

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('oshnisoft-hrm.employee-leave.index') }}" class="form-inline">
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
                                        <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.employee-leave.index') }}">X</a>
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
                                        <th>Application Date</th>
                                        <th>Leave Type</th>
                                        <th>Purpose</th>
                                        <th>Contact Number</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee_leaves as $val)
                                        <tr>
                                            <td>{{ $val->employee->employee_no }}</td>
                                            <td>{{ $val->employee->name }}</td>
                                            <td>{{ $val->application_date }}</td>
                                            <td>{{ $val->leaveType->name }}</td>
                                            <td>{{ $val->purpose }}</td>
                                            <td>{{ $val->contact_number }}</td>
                                            <td>{{ $val->start_date }}</td>
                                            <td>{{ $val->end_date }}</td>
                                            <td>{{ $val->day_count }}</td>
                                            <td>
                                                @if($val->approved_by != null)
                                                    {{ 'Approved [' . $val->approvedBy->name . ']'  }}
                                                @elseif ($val->authorized_by != null)
                                                    {{ 'Authorized [' . $val->authorizedBy->name . ']'  }}
                                                @else
                                                    {{ 'Pending' }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button"
                                                        data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show hr_employee-leave')
                                                        <li>
                                                            <a href="{{ route('oshnisoft-hrm.employee-leave.show', $val->id) . qString() }}"><i
                                                                    class="fa fa-eye"></i> Show</a>
                                                        </li>
                                                        @endcan

                                                        @can('edit hr_employee-leave')
                                                        <li><a
                                                                href="{{ route('oshnisoft-hrm.employee-leave.edit', $val->id) . qString() }}"><i
                                                                    class="fa fa-eye"></i> Edit</a>
                                                        </li>
                                                        @endcan

                                                        @can('delete hr_employee-leave')
                                                        <li><a
                                                                onclick="deleted('{{ route('oshnisoft-hrm.employee-leave.destroy', $val->id) . qString() }}')"><i
                                                                    class="fa fa-close"></i> Delete</a>
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
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection


@push('scripts')
<script>
    function getEmployeeLeaves() {
        var id = $('#employee_id').val();
        $('#leaveDiv').html('');
        if (id > 0) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                data: {id: id},
                url: "{{ route('oshnisoft-hrm.employee-leave.counts') }}",
                success: function(res) {
                    if (res.success) {
                        let html = `<table class="table table-bordered">
                        <tr>
                        <td></td>`;
                        res.data.name.forEach(element => {
                            html += `<th>${element}</th>`;
                        });
                        html += `</tr>
                        <tr>
                        <td>Policy</td>`;
                        res.data.day_count.forEach(element => {
                            html += `<td>${element}</td>`;
                        });
                        html += `</tr>
                        <tr>
                        <td>Taken</td>`;
                        res.data.leave_count.forEach(element => {
                            html += `<td>${element}</td>`;
                        });
                        html += `</tr>
                        <tr>
                        <td>Remain</td>`;
                        res.data.remain.forEach(element => {
                            html += `<td>${element}</td>`;
                        });
                        html += `</tr>
                        <table>`;

                        console.log(html);

                        $('#leaveDiv').html(html);
                    } else {
                        alert(res.message);
                    }
                },
                error: function(res) {
                    alert(res.message);
                }
            });
        }
    }

    @if(isset($edit))
        getEmployeeLeaves();
    @endif
</script>
@endpush
