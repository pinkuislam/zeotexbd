<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Oshnisoft\HRM\Models\Calendar;
use Oshnisoft\HRM\Models\Employee;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendance_date = $request->input('attendance_date', Date('Y-m-d'));
        $data = Employee::select([
            'employees.id',
            'employees.employee_no',
            'employees.name',
            'B.name as department',
            'C.name as designation',
            'D.name as workstation',
            'E.attendance_date',
            'E.login_time',
            'E.logout_time',
        ])
            ->leftJoin('employment_statuses', function ($q) {
                $q->on('employment_statuses.employee_id', '=', 'employees.id');
                $q->latest(1);
            })
            ->join('departments as B', function ($q) {
                $q->on('employment_statuses.department_id', '=', 'B.id');
            })
            ->join('designations as C', function ($q) {
                $q->on('employment_statuses.designation_id', '=', 'C.id');
            })
            ->join('work_stations as D', function ($q) {
                $q->on('employment_statuses.work_station_id', '=', 'D.id');
            })
            ->leftJoin('employee_attendances AS E', function ($q) use ($attendance_date) {
                $q->on('E.employee_id', '=', 'employees.id');
                $q->where('E.attendance_date', $attendance_date);
            })
            ->where('employees.status', 'Active')
            ->get();
        $calendar = Calendar::where('date', $attendance_date)->first();
        if ($calendar) {
            return view('hrm::employee-attendance', compact('data', 'calendar', 'attendance_date'))->with('attendance', 1);
        } else {
            return redirect()->action([CalendarController::class, 'index'])->with("errorMessage", "Please set calendar first!");
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            if (empty($request->employee_id)) {
                return redirect()->route("oshnisoft-hrm.employee-attendance.index")->with("errorMessage", "No Attendance data saved.");
            }

            $calendar = Calendar::where('date', $request->attendance_date)->firstOrFail();
            $inTime = $calendar->in_time;
            $outTime = $calendar->out_time;
            foreach ($request->employee_id as $key => $item) {
                $isLate = strtotime($request->login_time[$key]) > strtotime($inTime);
                $isEarly = strtotime($request->logout_time[$key]) < strtotime($outTime);
                DB::table("employee_attendances")
                    ->updateOrInsert(
                        [
                            "employee_id" => $item,
                            "attendance_date" => $request->attendance_date
                        ],
                        [
                            "login_time" => $request->login_time[$key],
                            "is_late" => $isLate ? 'Yes' : 'No',
                            "is_early" => $isEarly ? 'Yes' : 'No',
                            "logout_time" => $request->logout_time[$key]
                        ]
                    );
            }

            DB::commit();

            return redirect()->route("oshnisoft-hrm.employee-attendance.index")->with("successMessage", "Attendance data has been saved.");

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function reports(Request $request)
    {
        $attendance_date = $request->input('attendance_date', Date('Y-m-d'));
        $data = Employee::select([
            'employees.id',
            'employees.employee_no',
            'employees.name',
            'B.name as department',
            'C.name as designation',
            'D.name as workstation',
            'E.attendance_date',
            'E.login_time',
            'E.logout_time',
            'E.is_late',
            'E.is_early',
        ])
            ->leftJoin('employment_statuses', function ($q) {
                $q->on('employment_statuses.employee_id', '=', 'employees.id');
                $q->latest(1);
            })
            ->join('departments as B', function ($q) {
                $q->on('employment_statuses.department_id', '=', 'B.id');
            })
            ->join('designations as C', function ($q) {
                $q->on('employment_statuses.designation_id', '=', 'C.id');
            })
            ->join('work_stations as D', function ($q) {
                $q->on('employment_statuses.work_station_id', '=', 'D.id');
            })
            ->leftJoin('employee_attendances AS E', function ($q) use ($attendance_date) {
                $q->on('E.employee_id', '=', 'employees.id');
                $q->where('E.attendance_date', $attendance_date);
            })
            ->where('employees.status', 'Active')
            ->get();
        $calendar = Calendar::where('date', $attendance_date)->first();
        if ($calendar) {
            return view('hrm::employee-attendance', compact('data', 'calendar', 'attendance_date'))->with('dailyReport', 1);
        } else {
            return redirect()->action([CalendarController::class, 'index'])->with("errorMessage", "Please set calendar first!");
        }
    }

    public function exportPdf(Request $request)
    {
        $attendance_date = $request->input('attendance_date', Date('Y-m-d'));
        $employees = Employee::select([
            'employees.id',
            'employees.employee_no',
            'employees.name',
            'B.name as department',
            'C.name as designation',
            'D.name as workstation',
            'E.attendance_date',
            'E.login_time',
            'E.logout_time',
            'E.is_late',
            'E.is_early',
        ])
            ->leftJoin('employment_statuses', function ($q) {
                $q->on('employment_statuses.employee_id', '=', 'employees.id');
                $q->latest(1);
            })
            ->join('departments as B', function ($q) {
                $q->on('employment_statuses.department_id', '=', 'B.id');
            })
            ->join('designations as C', function ($q) {
                $q->on('employment_statuses.designation_id', '=', 'C.id');
            })
            ->join('work_stations as D', function ($q) {
                $q->on('employment_statuses.work_station_id', '=', 'D.id');
            })
            ->leftJoin('employee_attendances AS E', function ($q) use ($attendance_date) {
                $q->on('E.employee_id', '=', 'employees.id');
                $q->where('E.attendance_date', $attendance_date);
            })
            ->where('employees.status', 'Active')
            ->get();
        $calendar = Calendar::where('date', $attendance_date)->first();

        $data['employees'] = $employees;
        $data['attendance_date'] = $attendance_date;
        $data['calendar'] = $calendar;
        $pdf = PDF::loadview('hrm::daily-employee-attendance-pdf', $data);
        return $pdf->download('attendance-report-' . $attendance_date . '.pdf');
    }
    public function monthlyReports(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $daysInMonth = Carbon::now()->month($month)->daysInMonth;
        $data = Employee::with(['attendances' => function($q) use ($year, $month) {
            $q->select(['employee_id', 'attendance_date', 'login_time', 'logout_time']);
            $q->whereMonth('attendance_date', $month);
            $q->whereYear('attendance_date', $year);
        }])
        ->select('employees.id', 'employees.employee_no', 'employees.name')
        ->where('employees.status', 'Active')
        ->get();
        return view('hrm::employee-attendance', compact('data', 'year', 'month','daysInMonth'))->with('monthlyReport', 1);
    }
}
