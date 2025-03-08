
<div class="form-group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
    <label class="control-label col-sm-3">Employee ID/Name</label>
    <div class="col-sm-9">
        <select class="form-control select2" id="employee_id" name="employee_id" onchange="setEmployee()">
            <option value="">Select Employee ID/Name</option>
            @php ($employee_id = old('employee_id', isset($data) ? $data->employee_id : ''))
            @foreach(App\Models\HR\Employee::with('employmentStatus.designation')->get() as $emp)
                <option data-name="{{ $emp->name }}" data-code="{{ $emp->employee_no }}" value="{{ $emp->id }}" {{ ($emp->id == $employee_id) ? 'selected' : '' }}>{{ $emp->employee_no }} : {{ $emp->name }} : {{ ($emp->employmentStatus && $emp->employmentStatus->designation) ? $emp->employmentStatus->designation->name : 'N/A' }}</option>
            @endforeach
        </select>

        @if ($errors->has('employee_id'))
            <span class="help-block">{{ $errors->first('employee_id') }}</span>
        @endif
    </div>
</div>