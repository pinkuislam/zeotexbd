@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee-advance-salary.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Employee Advance Salary List
                    </a>
                </li>
                @can('add hr_employee-advance-salary')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('oshnisoft-hrm.employee-advance-salary.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Employee Advance Salary
                        </a>
                    </li>
                @endcan

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Employee Advance Salary
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Employee Advance Salary Details
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
                                    <td>{{ $data->employee->employee_no }}</td>
                                </tr>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>:</th>
                                    <td>{{ $data->employee->name }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ $data->date }}</td>
                                </tr>

                                <tr>
                                    <th>Payment Bank</th>
                                    <th>:</th>
                                    <td>{{ $data->bank->bank_name }}</td>
                                </tr>

                                <tr>
                                    <th>Installments</th>
                                    <th>:</th>
                                    <td>{{ $data->installment_count }}</td>
                                </tr>

                                <tr>
                                    <th>Deduction Start On</th>
                                    <th>:</th>
                                    <td>{{ $data->installment_count }}</td>
                                </tr>
                                <tr>
                                    <th>Advance Amount</th>
                                    <th>:</th>
                                    <td>{{ $data->amount }}</td>
                                </tr>
                                <tr>
                                    <th>Deducted Amount</th>
                                    <th>:</th>
                                    <td>{{ $data->deductAmount->sum('deduct_amount') }}</td>
                                </tr>
                                <tr>
                                    <th>Due Amount</th>
                                    <th>:</th>
                                    <td>{{ $data->amount - $data->deductAmount->sum('deduct_amount') }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{{ $data->note }}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <th>:</th>
                                    <td>{{ isset($data->createdBy) ? $data->createdBy->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <th>:</th>
                                    <td>{{ isset($data->updatedBy) ? $data->updatedBy->name : '' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="box-body table-responsive">
                            <h3 style="width:70%;padding:5px;border-bottom:1px solid gray;">Installments</h3>
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:120px;">Date</th>
                                        <th>Amount</th>
                                        <th>Staus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->installments as $installment)
                                    <tr>
                                    <td>{{ $installment->deduct_on }}</td>
                                    <td>{{ $installment->deduct_amount }}</td>
                                    <td>{{ $installment->status }}</td>
                                </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('oshnisoft-hrm.employee-advance-salary.update', $edit) : route('oshnisoft-hrm.employee-advance-salary.store') }}{{ qString() }}"
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
                                                <select name="employee_id" class="form-control">
                                                    <option value="">Select Employee</option>
                                                    @foreach ($employees as $employee)
                                                        <option value="{{ $employee->id }}"
                                                            {{ $employee_id == $employee->id ? 'selected' : '' }}>
                                                            {{ $employee->name . ' [' . $employee->employee_no . ']' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('employee_id'))
                                                    <span class="help-block">{{ $errors->first('employee_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('bank_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Bank:</label>
                                            <div class="col-sm-9">
                                                @php($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
                                                <select name="bank_id" class="form-control">
                                                    <option value="">Select Bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}"
                                                            {{ $bank_id == $bank->id ? 'selected' : '' }}>
                                                            {{ $bank->bank_name . ' [' . $bank->account_no . ']' }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('bank_id'))
                                                    <span class="help-block">{{ $errors->first('bank_id') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ old('date', isset($data) ? $data->date : date('Y-m-d')) }}"
                                                    required>

                                                @if ($errors->has('date'))
                                                    <span class="help-block">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>



                                        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Note:</label>
                                            <div class="col-sm-9">
                                                <textarea name="note" class="form-control" rows="3">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
                                                @if ($errors->has('note'))
                                                    <span class="help-block">{{ $errors->first('note') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-3">Amount:</label>
                                            <div class="col-sm-9">
                                                <input type="number" name="amount" class="form-control"
                                                    value="{{ old('amount', isset($data) ? $data->amount : '') }}" required>
                                                @if ($errors->has('amount'))
                                                    <span class="help-block">{{ $errors->first('amount') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="{{ $errors->has('deduct_type') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-3 required">Deduction Type:</label>
                                                <div class="col-sm-9">
                                                    <input type="radio" name="deduct_type" value="onetime" id="one_time"
                                                        @if (isset($data) && $data->deduct_type == 'onetime') checked @else checked @endif> <label
                                                        for="one_time" style="font-weight: normal;">onetime</label>

                                                    <input type="radio" name="deduct_type" value="installment"
                                                        id="installment" @if (isset($data) && $data->deduct_type == 'installment') checked @endif>
                                                    <label for="installment"
                                                        style="font-weight: normal;">installment</label>

                                                    @if ($errors->has('deduct_type'))
                                                        <span class="help-block">{{ $errors->first('deduct_type') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                        <div id="installment_count"
                                            class="form-group{{ $errors->has('installment_count') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Installment Count:</label>
                                            <div class="col-sm-9">
                                                <input type="number" name="installment_count" class="form-control"
                                                    value="{{ old('installment_count', isset($data) ? $data->installment_count : '') }}">
                                                @if ($errors->has('installment_count'))
                                                    <span
                                                        class="help-block">{{ $errors->first('installment_count') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('deduct_start_on') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Deduction Start On:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="deduct_start_on"
                                                    value="{{ old('deduct_start_on', isset($deduct_start_on) ? $data->deduct_start_on : date('Y-m-d')) }}"
                                                    required>

                                                @if ($errors->has('deduct_start_on'))
                                                    <span class="help-block">{{ $errors->first('deduct_start_on') }}</span>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <div class="col-sm-offset-3 text-center">
                                                <button type="submit"
                                                    class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                                <button type="reset" class="btn btn-warning btn-flat">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('oshnisoft-hrm.employee-advance-salary.index') }}"
                            class="form-inline">
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
                                        <button type="submit" class="btn btn-info btn-flat">Search</button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('oshnisoft-hrm.employee-advance-salary.index') }}">X</a>
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
                                        <th>Date</th>
                                        <th>Payment Bank</th>
                                        <th>Installments</th>
                                        <th>Deduction Sart On</th>
                                        <th>Advance Amount</th>
                                        <th>Deducted Amount</th>
                                        <th>Remaining Amount</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee_advance_salaries as $val)
                                        <tr>
                                            <td>{{ $val->employee->employee_no ?? '' }}</td>
                                            <td>{{ $val->employee->name ?? '' }}</td>
                                            <td>{{ $val->date }}</td>
                                            <td>{{ $val->bank->bank_name ?? '' }}</td>
                                            <td>{{ $val->installment_count }}</td>
                                            <td>{{ $val->deduct_start_on }}</td>
                                            <td>{{ $val->amount }}</td>
                                            <td>{{ $val->deductAmount->sum('deduct_amount') }}</td>
                                            <td>{{ $val->amount - $val->deductAmount->sum('deduct_amount') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show hr_employee-advance-salary')
                                                            <li>
                                                                <a
                                                                    href="{{ route('oshnisoft-hrm.employee-advance-salary.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a>
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
        $(document).ready(function() {
            $('#installment_count').hide();
        });
        $('input[type=radio][name=deduct_type]').change(function() {
            if (this.value == 'onetime') {
                $('#installment_count').hide();
            } else if (this.value == 'installment') {
                $('#installment_count').show();
            }
        });
    </script>
@endpush
