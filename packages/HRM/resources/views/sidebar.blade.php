@canany([
        'add hr_basic-setting',
        'list hr_calendar',
        'add hr_calendar',
        'list hr_department',
        'add
        hr_department',
        'list hr_designation',
        'add hr_designation',
        'list hr_work-station',
        'add
        hr_work-station',
        'list hr_leave-type',
        'add hr_leave-type',
        'list hr_overtime-policy',
        'add
        hr_overtime-policy',
        'list hr_bonus-setup',
        'add hr_bonus-setup',
        'list hr_salary',
    ])
    <li class="treeview {{ Request::routeIs('oshnisoft-hrm.*') ? 'active menu-open' : '' }}">
        <a href="#">
            <i class="fa fa-cog"></i>
            <span>HRM</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @canany([
                'add hr_basic-setting',
                'list hr_calendar',
                'add hr_calendar',
                'list
                hr_department',
                'add hr_department',
                'list hr_designation',
                'add hr_designation',
                'list
                hr_work-station',
                'add hr_work-station',
                'list hr_leave-type',
                'add hr_leave-type',
                'list
                hr_overtime-policy',
                'add hr_overtime-policy',
                'list hr_employee',
                'list hr_attendance',
                'list hr_employee-leave',
                'list hr_asset-requisition',
                ])
                <li
                    class="treeview {{ Request::routeIs('oshnisoft-hrm.hr-settings.*') || Request::routeIs('oshnisoft-hrm.calendars.*') || Request::routeIs('oshnisoft-hrm.department.*') || Request::routeIs('oshnisoft-hrm.designation.*') || Request::routeIs('oshnisoft-hrm.work-station.*') || Request::routeIs('oshnisoft-hrm.leave-type.*') || Request::routeIs('oshnisoft-hrm.overtime-policy.*') ? 'active menu-open' : '' }}">
                    <a href="#">
                        <i class="fa fa-cogs"></i>
                        <span>Settings</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        @can('add hr_basic-setting')
                            <li class="{{ Request::routeIs('oshnisoft-hrm.hr-settings.index') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.hr-settings.index') }}">
                                    <i class="fa fa fa-circle-o"></i>
                                    <span> Basic Settings </span>
                                </a>
                            </li>
                        @endcan

                        @canany(['list hr_calendar', 'add hr_calendar'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.calendars.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.calendars.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Calender </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_department', 'add hr_department'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.department.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.department.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Department </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_designation', 'add hr_designation'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.designation.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.designation.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Designation </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_work-station', 'add hr_work-station'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.work-station.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.work-station.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Workstation </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_leave-type', 'add hr_leave-type'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.leave-type.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.leave-type.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Leave Type </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_overtime-policy', 'add hr_overtime-policy'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.overtime-policy.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.overtime-policy.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Overtime Policy </span>
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany

            @canany([
                'list hr_employee',
                'list hr_attendance',
                'list hr_employee-leave',
                'list
                hr_asset-requisition',
                ])
                <li
                    class="treeview {{ Request::routeIs('oshnisoft-hrm.employee.*') || Request::routeIs('oshnisoft-hrm.employee-attendance.*') || Request::routeIs('oshnisoft-hrm.employee-leave.*') || Request::routeIs('oshnisoft-hrm.asset-requisition.*') ? 'active menu-open' : '' }}">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span>Employee</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        @canany(['list hr_employee', 'add hr_employee'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.employee.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Employee </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_attendance', 'add hr_attendance'])
                            <li
                                class="{{ Request::routeIs('oshnisoft-hrm.employee-attendance.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee-attendance.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Attendance</span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_employee-leave', 'add hr_employee-leave'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.employee-leave.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee-leave.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Leave </span>
                                </a>
                            </li>
                        @endcanany
                        @canany(['list hr_asset-requisition', 'add hr_asset-requisition'])
                            <li
                                class="{{ Request::routeIs('oshnisoft-hrm.asset-requisition.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.asset-requisition.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Asset Requisition </span>
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany

            @canany([
                'list hr_employee-incentive',
                'list hr_employee-consideration',
                'list
                hr_employee-penalty',
                'list hr_asset-requisition',
                'list hr_employee-consideration',
                'list
                hr_employee-penalty',
                'list hr_employee-overtime',
                'list hr_employee-expense-bill',
                'list
                hr_employee-advance-salary',
                ])
                <li
                    class="treeview {{ Request::routeIs('oshnisoft-hrm.employee-incentive.*') || Request::routeIs('oshnisoft-hrm.employee-consideration.*') || Request::routeIs('oshnisoft-hrm.employee-penalty.*') || Request::routeIs('oshnisoft-hrm.employee-overtime.*') || Request::routeIs('oshnisoft-hrm.employee-expense-bill.*') || Request::routeIs('oshnisoft-hrm.employee-advance-salary.*') ? 'active menu-open' : '' }}">
                    <a href="#">
                        <i class="fa fa-user"></i>
                        <span>Accounts</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">

                        @canany(['list hr_employee-incentive', 'add hr_employee-incentive'])
                            <li
                                class="{{ Request::routeIs('oshnisoft-hrm.employee-incentive.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee-incentive.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Incentive </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_employee-consideration', 'add hr_employee-consideration'])
                            <li
                                class="{{ Request::routeIs('oshnisoft-hrm.employee-consideration.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee-consideration.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Consideration </span>
                                </a>
                            </li>
                        @endcanany
                        @canany(['list hr_employee-penalty', 'add hr_employee-penalty'])
                            <li
                                class="{{ Request::routeIs('oshnisoft-hrm.employee-penalty.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee-penalty.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Deduction </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_employee-overtime', 'add hr_employee-overtime'])
                            <li
                                class="{{ Request::routeIs('oshnisoft-hrm.employee-overtime.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee-overtime.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Overtime </span>
                                </a>
                            </li>
                        @endcanany
                        @canany(['list hr_employee-expense-bill', 'add hr_employee-expense-bill'])
                            <li
                                class="{{ Request::routeIs('oshnisoft-hrm.employee-expense-bill.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee-expense-bill.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Expense Bill </span>
                                </a>
                            </li>
                        @endcanany
                        @canany(['list hr_employee-advance-salary', 'add hr_employee-advance-salary'])
                            <li
                                class="{{ Request::routeIs('oshnisoft-hrm.employee-advance-salary.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.employee-advance-salary.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Advance Salary </span>
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany

            @canany(['list hr_bonus-setup', 'add hr_bonus-setup', 'list hr_salary'])
                <li
                    class="treeview {{ Request::routeIs('oshnisoft-hrm.salaries.*') || Request::routeIs('oshnisoft-hrm.bonus-setup.*') ? 'active menu-open' : '' }}">
                    <a href="#">
                        <i class="fa fa-money"></i>
                        <span>Payroll</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">

                        @canany(['list hr_salary', 'add hr_salary'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.salaries.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.salaries.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Salary </span>
                                </a>
                            </li>
                        @endcanany

                        @canany(['list hr_bonus-setup', 'add hr_bonus-setup'])
                            <li class="{{ Request::routeIs('oshnisoft-hrm.bonus-setup.*') ? 'active' : '' }}">
                                <a href="{{ route('oshnisoft-hrm.bonus-setup.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span> Bonus Setup </span>
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany
        </ul>
    </li>
@endcanany
