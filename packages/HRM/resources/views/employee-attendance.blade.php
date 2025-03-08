@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($attendance) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee-attendance.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Attendance Entry
                    </a>
                </li>
                @can('report hr_employee-attendance')
                    <li {{ isset($dailyReport) ? 'class=active' : '' }}>
                        <a href="{{ route('oshnisoft-hrm.employee-attendance.reports') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Date-wise Report
                        </a>
                    </li>

                    <li {{ isset($monthlyReport) ? 'class=active' : '' }}>
                        <a href="{{ route('oshnisoft-hrm.employee-attendance.monthly-reports') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Monthly Report
                        </a>
                    </li>
                @endcan
            </ul>
            <div class="tab-content">
                @if (isset($attendance))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <div class="row">
                                <div class="col-sm-2 col-sm-offset-5">
                                    <form method="GET" action="{{ route('oshnisoft-hrm.employee-attendance.index') }}">
                                        <div class="form-group" style="text-align: center;">
                                            <label for="attendance_date">{{ __('Attendance Date') }}</label>

                                            <input type="text" name="attendance_date" id="attendance_date" class="form-control datepicker" value="{{ $attendance_date }}" />

                                            <br />

                                            <button class="btn btn-sm btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <form method="POST" action="{{ route('oshnisoft-hrm.employee-attendance.store') }}">
                                        @csrf
                                        {{-- $dayName = date("D", mktime(0, 0, 0, $month, $day, $year)); --}}
                                        <div class="form-group" style="text-align: center;">
                                            <label>{{ isset($calendar) ? $calendar->note : '' }}</label>

                                        </div>
                                        <input type="hidden" name="attendance_date" value="{{ $attendance_date }}" />

                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Staff ID</th>
                                                    <th>{{ __('Employee Name') }}</th>
                                                    <th>{{ __('Department') }}</th>
                                                    <th>{{ __('Designation') }}</th>
                                                    <th>{{ __('Login Time') }}</th>
                                                    <th>{{ __('Logout Time') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $emp)
                                                    <tr>
                                                        <td>{{ $emp->employee_no }}</td>
                                                        <td>{{ $emp->name }}</td>
                                                        <td>{{ $emp->department }}</td>
                                                        <td>{{ $emp->designation }}</td>

                                                        <td>
                                                            <input type="hidden" name="employee_id[]" value="{{ $emp->id }}" />

                                                            <input type="time" name="login_time[]" id="login_time" value="{{ isset($emp->login_time) ? $emp->login_time : $calendar->in_time }}" />
                                                        </td>

                                                        <td>
                                                            <input type="time" name="logout_time[]" id="logout_time" value="{{ isset($emp->logout_time) ? $emp->logout_time : $calendar->out_time }}" />
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>

                                            <tfoot>
                                                <tr style="text-align:center;">
                                                    <td colspan="5">
                                                        <button class="btn btn-md btn-success" onclick="saveAttendance()">Save</button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(isset($dailyReport))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <div class="row">
                                <div class="col-sm-2 col-sm-offset-5">
                                    <form method="GET" action="{{ route('oshnisoft-hrm.employee-attendance.reports') }}">
                                        <div class="form-group" style="text-align: center;">
                                            <label for="attendance_date">{{ __('Attendance Date') }}</label>

                                            <input type="text" name="attendance_date" id="attendance_date" class="form-control datepicker" value="{{ $attendance_date }}" />

                                            <br />

                                            <button class="btn btn-sm btn-primary">Submit</button>
                                            <a href="{{ route('oshnisoft-hrm.employee-attendance.reports-pdf') . '?attendance_date=' . $attendance_date }}"
                                                class="btn btn-primary btn-sm">Download</a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Staff ID</th>
                                                <th>Employee Name</th>
                                                <th>Department</th>
                                                <th>Designation</th>
                                                <th>In Time</th>
                                                <th>Late</th>
                                                <th>Out Time</th>
                                                <th>Early Leave</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $emp)
                                                <tr>
                                                    <td>{{ $emp->employee_no }}</td>
                                                    <td>{{ $emp->name }}</td>
                                                    <td>{{ $emp->department }}</td>
                                                    <td>{{ $emp->designation }}</td>

                                                    <td>
                                                        {{ $emp->login_time == null ? 'Absent' : date('h:i A', strtotime($emp->login_time)) }}
                                                    </td>

                                                    <td>
                                                        {{ $emp->login_time == null ? '' : $emp->is_late }}
                                                    </td>

                                                    <td>
                                                        {{ $emp->login_time == null ? '' :  date('h:i A', strtotime($emp->logout_time))}}
                                                    </td>
                                                    <td>
                                                        {{ $emp->login_time == null ? '' :  ($emp->logout_time == null ? '' : $emp->is_early) }}
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(isset($monthlyReport))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form method="GET" action="{{ route('oshnisoft-hrm.employee-attendance.monthly-reports') }}" class="form-inline">
                                        <div class="box-header text-right">
                                            <div class="row">
                                                <div class="form-group">
                                                    <select name="year" class="form-control">
                                                        @foreach (years() as $y)
                                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <select name="month" class="form-control">
                                                        @foreach (months() as $mk => $mv)
                                                            <option value="{{ $mk }}" {{ $month == $mk ? 'selected' : '' }}>{{ $mv }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                    
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-info btn-flat">Search</button>
                                                    <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.employee-attendance.monthly-reports') }}">X</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <td class="text-center" rowspan="2">Employee ID</td>
                                                <td class="text-center" rowspan="2">Employee Name</td>
                                                @for ($i = 0; $i < $daysInMonth; $i++)
                                                <td class="text-center" colspan="2">{{ $i + 1 . '-'. $month . '-'. $year}}</td>
                                                @endfor
                                            </tr>
                                            <tr>
                                                @for ($i = 0; $i < $daysInMonth; $i++)
                                                    <td style="padding: 1rem !important;" class="text-center">IN</td>
                                                    <td style="padding: 1rem !important;" class="text-center">OUT</td>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $emp)
                                                <tr>
                                                    <td>{{ $emp->employee_no }}</td>
                                                    <td>{{ $emp->name }}</td>
                                                    @for ($i = 0; $i < $daysInMonth; $i++)
                                                        @php
                                                          if ($i < 10){
                                                        $date = $year . '-'. $month . '-'. 0 .$i + 1 ; 
                                                        }else {
                                                            $date = $year . '-'. $month . '-'. $i + 1 ;
                                                        }
                                                        $attendance =  $emp->attendances->where('attendance_date', $date)->first();
                                                        @endphp
                                                        @if ($attendance)
                                                        <td>{{ date('h:i A', strtotime($attendance->login_time)) }}</td>
                                                        <td> {{ date('h:i A', strtotime($attendance->logout_time)) }} </td>
                                                        @else
                                                        <td></td>
                                                        <td></td>
                                                        @endif
                                                    @endfor
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
