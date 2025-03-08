@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="{{ route('oshnisoft-hrm.salaries.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Salary List
                    </a>
                </li>

                @can('add hr_salary')
                <li>
                    <a href="{{ route('oshnisoft-hrm.salaries.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Salary
                    </a>
                </li>
                @endcan
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('oshnisoft-hrm.salaries.index') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select name="employee" class="form-control">
                                        <option value="">All Employee</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ Request::get('employee') == $emp->id ? 'selected' : '' }}>{{ $emp->name }} [{{ $emp->employee_no }}]</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select name="year" class="form-control">
                                        <option value="">All Year</option>
                                        @foreach (years() as $y)
                                            <option value="{{ $y }}" {{ Request::get('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select name="month" class="form-control">
                                        <option value="">All Month</option>
                                        @foreach (months() as $mk => $mv)
                                            <option value="{{ $mk }}" {{ Request::get('month') == $mk ? 'selected' : '' }}>{{ $mv }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        @foreach (['Processed', 'Paid'] as $sts)
                                            <option value="{{ $sts }}" {{ Request::get('status') == $sts ? 'selected' : '' }}>{{ $sts }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit"
                                        class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.salaries.index') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body">
                        <form method="POST" action="{{ route('oshnisoft-hrm.salaries.payment') }}">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="chk_all" value="1" onclick="chkAll()"></th>
                                            <th>Month</th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Joining Date</th>
                                            <th>Gross Salary</th>
                                            <th>Phone Bill</th>
                                            <th>Overtime</th>
                                            <th>Bonus</th>
                                            <th>Expense Bill</th>
                                            <th>Consideration</th>
                                            <th>Incentive</th>
                                            <th>Penalty</th>
                                            <th>Salary Advance</th>
                                            <th>Provident Fund</th>
                                            <th>Income Tax</th>
                                            <th>Net Salary</th></th>
                                            <th>Status</th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records as $val)
                                        <tr>
                                            <td>
                                                @if ($val->status != 'Paid')
                                                <input type="checkbox" class="chk" name="ids[]" value="{{ $val->id }}">
                                                @else
                                                <i class="fa fa-check-square text-success"></i>
                                                @endif
                                            </td>
                                            <td>{{ monthFormat($val->salary_date) }}</td>
                                            <td>{{ $val->employee->employee_no ?? '-' }}</td>
                                            <td>{{ $val->employee->name ?? '-' }}</td>
                                            <td>{{ $val->employee->employmentStatus->department->name ?? '-' }}</td>
                                            <td>{{ $val->employee->employmentStatus->designation->name ?? '-' }}</td>
                                            <td>{{ $val->employee->org_joining_date ?? '-' }}</td>
                                            <td>{{ $val->gross_salary }}</td>
                                            <td>{{ $val->mobile_bill }}</td>
                                            <td>{{ $val->overtime_amount }}</td>
                                            <td>{{ $val->bonus_amount }}</td>
                                            <td>{{ $val->expense_amount }}</td>
                                            <td>{{ $val->consider_amount }}</td>
                                            <td>{{ $val->incentive_amount }}</td>
                                            <td>{{ $val->penalty_amount }}</td>
                                            <td>{{ $val->advance_amount }}</td>
                                            <td>{{ $val->pf_deduction }}</td>
                                            <td>{{ $val->income_tax }}</td>
                                            <td>{{ $val->net_salary }}</td>
                                            <td>{{ $val->status }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-right" id="btnDiv" style="display: none;">
                                <button type="submit" name="action" value="export" class="btn btn-warning btn-flat">Export Excel</button>
                                @can('payment hr_salary')
                                <button type="submit" name="action" value="payment" class="btn btn-success btn-flat">Payment</button>
                                @endcan
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    function chkAll() {
        if (document.getElementById('chk_all').checked == true) {
            $('.chk').prop('checked', true);
        } else {
            $('.chk').prop('checked', false);
        }

        if ($('.chk').filter(':checked').length > 0) {
            $('#btnDiv').show();
        } else {
            $('#btnDiv').hide();
        }
    }
</script>
@endpush
