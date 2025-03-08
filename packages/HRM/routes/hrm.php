<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Oshnisoft\HRM\Http\Controllers',
    'middleware' => array_merge(['web'], config('hrm.middleware')),
    'prefix' => 'hr', 'as' => 'oshnisoft-hrm.'
], function () {
    Route::resource('calendars', 'CalendarController')->only('index', 'store', 'update');
    Route::post('calendars/{year}/generate', 'CalendarController@generateCalendar')->name('calendars.generate');

    Route::resource('department', 'DepartmentController');
    Route::put('department/{id}/status', 'DepartmentController@statusChange')->name('department.status');

    Route::resource('designation', 'DesignationController');
    Route::put('designation/{id}/status', 'DesignationController@statusChange')->name('designation.status');

    Route::resource('work-station', 'WorkStationController');
    Route::put('work-station/{id}/status', 'WorkStationController@statusChange')->name('work-station.status');

    Route::resource('leave-type', 'LeaveTypeController');
    Route::put('leave-type/{id}/status', 'LeaveTypeController@statusChange')->name('leave-type.status');

    Route::resource('overtime-policy', 'OvertimePolicyController');
    Route::put('overtime-policy/{id}/status', 'OvertimePolicyController@statusChange')->name('overtime-policy.status');

    Route::resource('employee', 'EmployeeController');
    Route::put('employee/{id}/status', 'EmployeeController@statusChange')->name('employee.status');
    Route::get('employee/{id}/employment', 'EmployeeController@employment')->name('employee.employment');
    Route::put('employee/{id}/update-employment', 'EmployeeController@updateEmployment')->name('employee.update-employment');
    Route::get('employee/{id}/salary', 'EmployeeController@salary')->name('employee.salary');
    Route::post('employee/{id}/salary-update', 'EmployeeController@salaryUpdate')->name('employee.salary-update');
    Route::get('employee-export', 'EmployeeController@export')->name('employee.export');

    Route::get('employee-leave/counts', 'EmployeeLeaveController@counts')->name('employee-leave.counts');
    Route::resource('employee-leave', 'EmployeeLeaveController');
    Route::put('employee-leave/{id}/authorize', 'EmployeeLeaveController@recommend')->name('employee-leave.authorize');
    Route::put('employee-leave/{id}/approve', 'EmployeeLeaveController@approve')->name('employee-leave.approve');

    Route::resource('employee-incentive', 'EmployeeIncentiveController');
    Route::put('employee-incentive/{id}/approve', 'EmployeeIncentiveController@approve')->name('employee-incentive.approve');
    Route::put('employee-incentive/{id}/reject', 'EmployeeIncentiveController@reject')->name('employee-incentive.reject');

    Route::resource('employee-consideration', 'EmployeeConsiderationController');
    Route::put('employee-consideration/{id}/approve', 'EmployeeConsiderationController@approve')->name('employee-consideration.approve');
    Route::put('employee-consideration/{id}/reject', 'EmployeeConsiderationController@reject')->name('employee-consideration.reject');

    Route::resource('employee-penalty', 'EmployeePenaltyController');
    Route::put('employee-penalty/{id}/approve', 'EmployeePenaltyController@approve')->name('employee-penalty.approve');
    Route::put('employee-penalty/{id}/reject', 'EmployeePenaltyController@reject')->name('employee-penalty.reject');

    Route::resource('employee-overtime', 'EmployeeOvertimeController');
    Route::put('employee-overtime/{id}/approve', 'EmployeeOvertimeController@approve')->name('employee-overtime.approve');
    Route::put('employee-overtime/{id}/reject', 'EmployeeOvertimeController@reject')->name('employee-overtime.reject');

    Route::resource('employee-expense-bill', 'EmployeeExpenseBillController');
    Route::put('employee-expense-bill/{id}/approve', 'EmployeeExpenseBillController@approve')->name('employee-expense-bill.approve');
    Route::put('employee-expense-bill/{id}/reject', 'EmployeeExpenseBillController@reject')->name('employee-expense-bill.reject');

    Route::resource('employee-attendance', 'EmployeeAttendanceController')->only('index', 'store');
    Route::get('employee-attendance/date-report', 'EmployeeAttendanceController@reports')->name('employee-attendance.reports');
    Route::get('employee-attendance/month-report', 'EmployeeAttendanceController@monthlyReports')->name('employee-attendance.monthly-reports');
    Route::get('employee-attendance/date-report-pdf', 'EmployeeAttendanceController@exportPdf')->name('employee-attendance.reports-pdf');

    Route::resource('employee-advance-salary', 'EmployeeAdvanceSalaryController')->only('index', 'create', 'store', 'show');
    Route::resource('hr-settings', 'SettingsController')->only(['index', 'store']);

    Route::resource('asset-requisition', 'AssetRequisitionController')->only('index', 'show', 'update');

    Route::resource('bonus-setup', 'BonusSetupController');
    Route::put('bonus-setup/{id}/reject', 'BonusSetupController@reject')->name('bonus-setup.reject');

    Route::group(['prefix' => 'salaries', 'as' => 'salaries.'], function () {
        Route::post('payment', 'SalaryController@payment')->name('payment');
    });
    Route::resource('salaries', 'SalaryController')->only('index', 'create', 'store');
});
