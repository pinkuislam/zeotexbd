@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('oshnisoft-hrm.salaries.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Salary List
                    </a>
                </li>

                @can('add hr_salary')
                <li class="active">
                    <a href="{{ route('oshnisoft-hrm.salaries.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Salary
                    </a>
                </li>
                @endcan
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('oshnisoft-hrm.salaries.create') }}" class="form-inline">
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
                                    <button type="submit"
                                        class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.salaries.create') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body">
                        <form method="POST" action="{{ route('oshnisoft-hrm.salaries.store') }}">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="chk_all" value="1" onclick="chkAll()"></th>
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
                                            <th>Penalty (-)</th>
                                            <th>Salary Advance (-)</th>
                                            <th>Provident Fund (-)</th>
                                            <th>Income Tax  (-)</th>
                                            <th>Net Salary</th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records as $val)
                                        <tr>
                                            <td><input type="checkbox" class="chk" name="ids[]" value="{{ $val->id }}"></td>
                                            <td>{{ $val->employee_no }}</td>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ $val->department_name }}</td>
                                            <td>{{ $val->designation_name }}</td>
                                            <td>{{ $val->org_joining_date }}</td>
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
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-right" id="btnDiv" style="display: none;">
                                <input type="hidden" name="year" value="{{ $year }}" required>
                                <input type="hidden" name="month" value="{{ $month }}" required>
                                <button type="submit" name="action" value="export" class="btn btn-warning btn-flat">Export Excel</button>
                                <button type="submit" name="action" value="process" class="btn btn-success btn-flat">Process</button>
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
