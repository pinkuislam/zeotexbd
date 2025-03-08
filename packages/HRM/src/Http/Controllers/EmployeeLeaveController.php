<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\EmployeeLeave;
use Oshnisoft\HRM\Models\LeaveType;
use Sudip\MediaUploader\Facades\MediaUploader;

class EmployeeLeaveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_employee-leave');
        $sql = EmployeeLeave::orderBy('created_at', 'ASC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('date', 'LIKE', '%' . $request->q . '%');
            });
        }


        $employee_leaves = $sql->get();

        return view('hrm::employee-leave', compact('employee_leaves'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_employee-leave');
        $employees = Employee::where('status', 'Active')->get();
        $leaveTypes = LeaveType::where('status', 'Active')->get();
        return view('hrm::employee-leave', compact('employees', 'leaveTypes'))->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'leave_type_id' => 'required|integer',
            'purpose' => 'required|max:255',
            'application_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $storeData = [
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'contact_number' => $request->contact_number,
            'purpose' => $request->purpose,
            'application_date' => dbDateFormat($request->application_date),
            'start_date' => dbDateFormat($request->start_date),
            'end_date' => dbDateFormat($request->end_date),
            'day_count' => $this->dateDiffDays($request->start_date, $request->end_date),
            'created_by' => Auth::user()->id,
        ];

        if ($request->has('status')) {
            $status = $request->status;
            $storeData['status'] = $status;
            if ($status == 'Authorized') {
                $storeData['authorized_by'] = Auth::user()->id;
            } else if ($status == 'Approved') {
                $storeData['approved_by'] = Auth::user()->id;
            }
        } else {
            $storeData['status'] = 'Pending';
        }

        if ($request->has('pay_type')) {
            $storeData['pay_type'] = $request->pay_type;
        } else {
            $storeData['pay_type'] = 'Paid';
        }


        if ($request->hasFile('attachment')) {
            $file = MediaUploader::imageUpload($request->attachment, 'leaves', 1, null, [600, 600], [80, 80]);
            $storeData['attachment'] = $file['name'];
        }
        EmployeeLeave::create($storeData);
        $request->session()->flash('successMessage', 'Leave Application was successfully added!');
        return redirect()->route('oshnisoft-hrm.employee-leave.create', qArray());
    }

    private function dateDiffDays($fdate, $tdate)
    {
        $datetime1 = new DateTime($fdate);
        $datetime2 = new DateTime($tdate);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');

        return $days + 1;
    }

    public function show(Request $request, $id)
    {
        $data = EmployeeLeave::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }

        $year = date('Y');
        $previousLeaves = EmployeeLeave::where('employee_id', $data->employee_id)->whereYear('application_date', $year)->where('leave_type_id', $data->leave_type_id)->whereNotNull('approved_by')->sum('day_count');

        return view('hrm::employee-leave', compact('data', 'previousLeaves'))->with('show', $id);
    }

    public function edit(Request $request, $id)
    {
        $data = EmployeeLeave::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }

        $employees = Employee::where('status', 'Active')->get();
        $leaveTypes = LeaveType::where('status', 'Active')->get();

        return view('hrm::employee-leave', compact('data', 'employees', 'leaveTypes'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'employee_id' => 'required|integer',
            'leave_type_id' => 'required|integer',
            'purpose' => 'required|max:255',
            'application_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);


        $data = EmployeeLeave::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }

        $updateData = [
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'contact_number' => $request->contact_number,
            'purpose' => $request->purpose,
            'application_date' => dbDateFormat($request->application_date),
            'start_date' => dbDateFormat($request->start_date),
            'end_date' => dbDateFormat($request->end_date),
            'day_count' => $this->dateDiffDays($request->start_date, $request->end_date),
            'updated_by' => Auth::user()->id,
        ];


        if ($request->hasFile('attachment')) {
            if ($data->attachment) {
                MediaUploader::delete('leaves', $data->attachment, true);
            }
            $file = MediaUploader::imageUpload($request->attachment, 'leaves', 1, null, [600, 600], [80, 80]);
            $updateData['attachment'] = $file['name'];
        }

        if ($request->has('status')) {
            $status = $request->status;
            $storeData['status'] = $status;
            if ($status == 'Authorized') {
                $storeData['authorized_by'] = Auth::user()->id;
            } else if ($status == 'Approved') {
                $storeData['approved_by'] = Auth::user()->id;
            }
        }

        if ($request->has('pay_type')) {
            $storeData['pay_type'] = $request->pay_type;
        }

        $data->update($updateData);

        $request->session()->flash('successMessage', 'Employee Leave was successfully updated!');
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function counts(Request $request)
    {
        $credentials = $request->only('id');
        $validator = Validator::make($credentials, [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => implode(", ", $validator->messages()->all())], 200);
        }

        $data = [];
        $types = LeaveType::select('id', 'name', 'day_count')->where('status', 'Active')->get();
        $leaves = EmployeeLeave::select('leave_type_id', DB::raw('SUM(day_count) AS leave_count'))
            ->where('employee_id', $request->id)
            ->groupBy('leave_type_id')
            ->pluck('leave_count', 'leave_type_id')
            ->toArray();

        foreach ($types as $key => $val) {
            $leaveCount = isset($leaves[$val->id]) ? $leaves[$val->id] : 0;
            $data['name'][] = $val->name;
            $data['day_count'][] = $val->day_count;
            $data['leave_count'][] = $leaveCount;
            $data['remain'][] = ($val->day_count - $leaveCount);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function destroy(Request $request, $id)
    {
        $data = EmployeeLeave::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->action([self::class, 'index'], qArray());
        }
        if ($data->attachment) {
            MediaUploader::delete('leaves', $data->attachment, true);
        }
        $data->delete();

        $request->session()->flash('successMessage', 'EmployeeLeave was successfully deleted!');
        return redirect()->action([self::class, 'index'], qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = EmployeeLeave::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.employee-leave.index")->with("successMessage", "EmployeeLeave status was successfully changed!");
    }

    public function recommend(Request $request, $id)
    {
        $data = EmployeeLeave::find($id);
        $storeData = [
            'status' => 'Authorized',
            'authorized_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Authorized!');
        return redirect()->action([self::class, 'index']);
    }

    public function approve(Request $request, $id)
    {
        $data = EmployeeLeave::find($id);
        $storeData = [
            'status' => 'Approved',
            'approved_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ];

        $data->update($storeData);
        $request->session()->flash('successMessage', 'Successfully Approved!');
        return redirect()->action([self::class, 'index']);
    }
}
