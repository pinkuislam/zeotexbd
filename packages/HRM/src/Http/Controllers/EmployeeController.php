<?php

namespace Oshnisoft\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Oshnisoft\HRM\Exports\EmployeesExport;
use Oshnisoft\HRM\Models\BasicSettings;
use Oshnisoft\HRM\Models\Department;
use Oshnisoft\HRM\Models\Designation;
use Oshnisoft\HRM\Models\Employee;
use Oshnisoft\HRM\Models\EmployeeEducation;
use Oshnisoft\HRM\Models\EmployeeExperience;
use Oshnisoft\HRM\Models\EmployeeSalarySetup;
use Oshnisoft\HRM\Models\EmploymentStatus;
use Oshnisoft\HRM\Models\WorkStation;
use Sudip\MediaUploader\Facades\MediaUploader;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('list hr_employee');

        $sql = Employee::orderBy('employee_no', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('employee_no', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('contact_no', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $employees = $sql->get();

        return view('hrm::employee', compact('employees'))->with('list', 1);
    }


    public function create()
    {
        $this->authorize('add hr_employee');
        $departments = Department::where('status', 'Active')->get();
        $designations = Designation::where('status', 'Active')->get();
        $workstations = WorkStation::where('status', 'Active')->get();
        $employees = Employee::where('status', 'Active')->get();
        return view('hrm::employee', compact('departments', 'designations', 'workstations', 'employees'))->with('create', 1);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'employee_no' => 'required|max:100|unique:employees,employee_no',
            'father_name' => 'required|max:255',
            'mother_name' => 'required|max:255',
            'birth_date' => 'required|date_format:Y-m-d',
            'gender' => 'required',
            'nationality' => 'required|max:255',
            'present_address' => 'required|max:255',
            'religion' => 'required|in:Islam,Hinduism,Christian,Buddhism',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $employeeData = [
            'name' => $request->name,
            'employee_no' => $request->employee_no,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            "org_joining_date" => $request->org_joining_date,
            "religion" => $request->religion,
            "blood_group" => $request->blood_group,
            "contact_no" => $request->contact_no,
            "email" => $request->email,
            "present_address" => $request->present_address,
            "permanent_address" => $request->permanent_address,
            'status' => $request->status,
            'created_by' => Auth::user()->id,
        ];
        if ($request->hasFile('image')) {
            $file = MediaUploader::imageUpload($request->image, 'employees', 1, null, [600, 600], [80, 80]);
            $employeeData['image'] = $file['name'];
        }

        if ($request->hasFile('nid_front_image')) {
            $file = MediaUploader::imageUpload($request->nid_front_image, 'employees', 1, null, [600, 600], [80, 80]);
            $employeeData['nid_front_image'] = $file['name'];
        }

        if ($request->hasFile('nid_back_image')) {
            $file = MediaUploader::imageUpload($request->nid_front_image, 'employees', 1, null, [600, 600], [80, 80]);
            $employeeData['nid_back_image'] = $file['name'];
        }

        try {
            DB::beginTransaction();
            $employee = Employee::create($employeeData);

            if ($request->only('education_id')) {
                $educationData = [];
                foreach ($request->education_id as $key => $expId) {
                    $experienceData[] = [
                        'employee_id' => $employee->id,
                        'degree' => $request->degree[$key],
                        'institution' => $request->institution[$key],
                        'board_university' => $request->board_university[$key],
                        'group_subject' => $request->group_subject[$key],
                        'result' => $request->result[$key],
                        'passing_year' => $request->passing_year[$key],
                        'created_at' => now(),
                    ];
                }
                EmployeeEducation::insert($experienceData);
            }

            if ($request->only('experience_id')) {
                $experienceData = [];
                foreach ($request->experience_id as $key => $expId) {
                    $experienceData[] = [
                        'employee_id' => $employee->id,
                        'organization' => $request->organization[$key],
                        'role' => $request->role[$key],
                        'responsibility' => $request->responsibility[$key],
                        'joining_date' => $request->joining_date[$key],
                        'last_working_date' => $request->last_working_date[$key],
                        'duration' => $request->duration[$key],
                        'created_at' => now(),
                    ];
                }
                EmployeeExperience::insert($experienceData);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return redirect()->route("oshnisoft-hrm.employee.index")->with("errorMessage", "Fail to save new Employee data.");
        }


        $request->session()->flash('successMessage', 'Employee was successfully added!');
        return redirect()->route('oshnisoft-hrm.employee.create', qArray());
    }

    public function employment(Request $request, $id)
    {

        $employeeData = Employee::find($id);

        if (empty($employeeData)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee.index', qArray());
        }

        $data = $employeeData->employmentStatus;

        $departments = Department::where('status', 'Active')->get();
        $designations = Designation::where('status', 'Active')->get();
        $workstations = WorkStation::where('status', 'Active')->get();
        $employees = Employee::where('status', 'Active')->get();
        if (isset($data->employment))
            $esEdit = $id;
        else
            $esEdit = 0;

        return view('hrm::employee', compact('data', 'employeeData', 'departments', 'designations', 'workstations', 'employees', 'esEdit'))->with('employment', $id);
    }

    public function updateEmployment(Request $request, $id)
    {

        $data = Employee::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee.index', qArray());
        }

        $employmentData = $data->employmentStatus;
        if (isset($employmentData)) {
            if ($request->status == 'Confirm') {
                if ($employmentData->status == 'Probation') {
                    if (date('Y-m-d') <= $employmentData->probation_end_on) {
                        $request->session()->flash('errorMessage', 'Employee has not been confirmed before ' . $employmentData->probation_end_on . ' !');
                        return redirect()->route('oshnisoft-hrm.employee.index', qArray());
                    }
                }
            }

        }
        $storeData = [
            'employee_id' => $data->id,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'work_station_id' => $request->work_station_id,
            'supervisor_id' => $request->supervisor_id,
            'status' => $request->status,
            'probation_end_on' => $request->probation_end_on,
            'remarks' => $request->remarks,
            'created_by' => Auth::user()->id,
            'effect_date' => now(),
            'created_at' => now(),
        ];
        EmploymentStatus::create($storeData);
        return redirect()->route("oshnisoft-hrm.employee.index")->with("successMessage", "Employee Employment status was successfully stored!");
    }


    public function show(Request $request, $id)
    {
        $data = Employee::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee.index', qArray());
        }

        return view('hrm::employee', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = Employee::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee.index', qArray());
        }
        $departments = Department::where('status', 'Active')->get();
        $designations = Designation::where('status', 'Active')->get();
        $workstations = WorkStation::where('status', 'Active')->get();
        $employees = Employee::where('status', 'Active')->get();

        return view('hrm::employee', compact('data', 'departments', 'designations', 'workstations', 'employees'))->with('edit', $id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'employee_no' => 'required|max:100|unique:employees,employee_no,' . $id . ',id',
            'father_name' => 'required|max:255',
            'mother_name' => 'required|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'nationality' => 'required|max:255',
            'present_address' => 'required|max:255',
            'religion' => 'required|in:Islam, Hinduism, Christian,Buddhism',
            'status' => 'required|in:Active,Deactivated',
        ]);

        $employeeData = [
            'name' => $request->name,
            'employee_no' => $request->employee_no,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            "org_joining_date" => $request->org_joining_date,
            "religion" => $request->religion,
            "blood_group" => $request->blood_group,
            "contact_no" => $request->contact_no,
            "email" => $request->email,
            "present_address" => $request->present_address,
            "permanent_address" => $request->permanent_address,
            'status' => $request->status,
            'updated_by' => Auth::user()->id,
        ];

        $data = Employee::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('oshnisoft-hrm.employee.index', qArray());
        }

        if ($request->hasFile('image')) {
            if ($data->image) {
                MediaUploader::delete('employees', $data->image, true);
            }
            $file = MediaUploader::imageUpload($request->image, 'employees', 1, null, [600, 600], [80, 80]);
            $employeeData['image'] = $file['name'];
        }

        if ($request->hasFile('nid_front_image')) {
            if ($data->nid_front_image) {
                MediaUploader::delete('employees', $data->nid_front_image, true);
            }

            $file = MediaUploader::imageUpload($request->nid_front_image, 'employees', 1, null, [600, 600], [80, 80]);
            $employeeData['nid_front_image'] = $file['name'];
        }

        if ($request->hasFile('nid_back_image')) {
            if ($data->nid_back_image) {
                MediaUploader::delete('employees', $data->nid_back_image, true);
            }

            $file = MediaUploader::imageUpload($request->nid_back_image, 'employees', 1, null, [600, 600], [80, 80]);
            $employeeData['nid_back_image'] = $file['name'];
        }

        try {
            DB::beginTransaction();
            EmployeeExperience::where('employee_id', $data->id)->delete();
            EmployeeEducation::where('employee_id', $data->id)->delete();
            $data->update($employeeData);
            if ($request->only('education_id')) {
                $educationData = [];
                foreach ($request->education_id as $key => $expId) {
                    if (!empty($request->degree[$key]) && !empty($request->result[$key]) && !empty($request->group_subject[$key]) && !empty($request->passing_year[$key])) {
                        $educationData[] = [
                            'employee_id' => $data->id,
                            'degree' => $request->degree[$key],
                            'institution' => $request->institution[$key],
                            'board_university' => $request->board_university[$key],
                            'group_subject' => $request->group_subject[$key],
                            'result' => $request->result[$key],
                            'passing_year' => $request->passing_year[$key],
                            'created_at' => now(),
                        ];
                    }
                }
                EmployeeEducation::insert($educationData);
            }

            if ($request->only('experience_id')) {
                $experienceData = [];
                foreach ($request->experience_id as $key => $expId) {
                    if (!empty($request->organization[$key])) {
                        $experienceData[] = [
                            'employee_id' => $data->id,
                            'organization' => $request->organization[$key],
                            'role' => $request->role[$key],
                            'responsibility' => $request->responsibility[$key],
                            'joining_date' => $request->joining_date[$key],
                            'last_working_date' => $request->last_working_date[$key],
                            'duration' => $request->duration[$key],
                            'created_at' => now(),
                        ];
                    }
                }
                EmployeeExperience::insert($experienceData);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route("oshnisoft-hrm.employee.index")->with("errorMessage", "Fail to update Employee data.");
        }

        $request->session()->flash('successMessage', 'Employee was successfully updated!');
        return redirect()->route('oshnisoft-hrm.employee.index', qArray());
    }


    public function destroy(Request $request, $id)
    {
        $data = Employee::find($id);
        if (empty($data)) {
            $request->session()->flash('errorMessage', 'Data not found!');
            return redirect()->route('employee.index', qArray());
        }

        $data->delete();

        $request->session()->flash('successMessage', 'Employee was successfully deleted!');
        return redirect()->route('oshnisoft-hrm.employee.index', qArray());
    }


    public function statusChange(Request $request, $id)
    {
        $data = Employee::findOrFail($id);
        $data->update(['status' => ($data->status == 'Active' ? 'Deactivated' : 'Active'), 'updated_by' => Auth::user()->id]);

        return redirect()->route("oshnisoft-hrm.employee.index")->with("successMessage", "Employee status was successfully changed!");
    }

    public function export(Request $request)
    {
        $this->authorize('list employee');

        return Excel::download(new EmployeesExport, 'employee-list-' . time() . '.xlsx');
    }

    public function salary(Request $request, $id)
    {
        $salary_structure_qry = BasicSettings::where("name", "salary_structure")
            ->get()
            ->pluck("value", "name")
            ->toArray();
        $salaryStructure = isset($salary_structure_qry['salary_structure']) ? json_decode($salary_structure_qry['salary_structure'], true) : [];
        $data = Employee::where('id', $id)->first();
        $salaryData = EmployeeSalarySetup::where("employee_id", $id)->first();
        $banks = Bank::where('status', 'Active')->get();

        return view('hrm::employee', compact('data', 'salaryData', 'salaryStructure', 'banks'))->with('salary', $id);

    }

    public function salaryUpdate(Request $request, $id)
    {
        $salaryData = EmployeeSalarySetup::where("employee_id", $id)->first();
        if ($salaryData) {
            $salaryData->update([
                "basic_salary" => $request->basic_salary,
                "house_rent" => $request->house_rent,
                "medical_allowance" => $request->medical_allowance,
                "conveyance_allowance" => $request->conveyance_allowance,
                "entertainment_allowance" => $request->entertainment_allowance,
                "other_allowance" => $request->other_allowance,
                "income_tax" => $request->income_tax,
                "pf_deduction" => $request->pf_deduction,
                "mobile_bill" => $request->mobile_bill,
                "gross_salary" => $request->gross_salary,
                "bank_acc_no" => $request->bank_acc_no,
                "bank_id" => $request->bank_id,
                "updated_at" => now(),
                "updated_by" => Auth::user()->id,
            ]);
            return redirect()->route("oshnisoft-hrm.employee.index")->with("successMessage", "Employee salary updated successfully.");
        } else {
            EmployeeSalarySetup::insert([
                "employee_id" => $id,
                "basic_salary" => $request->basic_salary,
                "house_rent" => $request->house_rent,
                "medical_allowance" => $request->medical_allowance,
                "conveyance_allowance" => $request->conveyance_allowance,
                "entertainment_allowance" => $request->entertainment_allowance,
                "other_allowance" => $request->other_allowance,
                "income_tax" => $request->income_tax,
                "pf_deduction" => $request->pf_deduction,
                "mobile_bill" => $request->mobile_bill,
                "gross_salary" => $request->gross_salary,
                "bank_acc_no" => $request->bank_acc_no,
                "bank_id" => $request->bank_id,
                "created_by" => Auth::user()->id,
            ]);
            return redirect()->route("oshnisoft-hrm.employee.index")->with("successMessage", "Employee salary assigned successfully.");
        }


    }
}
