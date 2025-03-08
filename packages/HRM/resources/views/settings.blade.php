@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="box-body table-responsive">
                        <div class="row">
                            <div class="col-sm-2 col-sm-offset-5">
                                <h3 style="text-align: center;text-decoration: underline;">Basic Settings</h3>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('oshnisoft-hrm.hr-settings.store') }}" class="form-horizontal">
                            @csrf

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group{{ $errors->has('in_time') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-6 required">In Time:</label>
                                        <div class="col-sm-6">
                                            <input type="time" class="form-control timepicker" name="in_time" value="{{ isset($data['in_time']) ? $data['in_time'] : '' }}" required>

                                            @if ($errors->has('in_time'))
                                                <span class="help-block">{{ $errors->first('in_time') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group{{ $errors->has('out_time') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-6 required">Out Time:</label>
                                        <div class="col-sm-6">
                                            <input type="time" class="form-control timepicker" name="out_time" value="{{ isset($data['out_time']) ? $data['out_time'] : '' }}" required>

                                            @if ($errors->has('out_time'))
                                                <span class="help-block">{{ $errors->first('out_time') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group{{ $errors->has('weekend') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-2 required">Weekend:</label>

                                        <div class="col-sm-8">
                                            <input type="checkbox" id="fri" name="weekend[]" value="Fri" @if(in_array("Fri", $data['weekend'])) checked @endif >
                                            <label for="fri">Friday</label> &emsp;

                                            <input type="checkbox" id="sat" name="weekend[]" value="Sat" @if(in_array("Sat", $data['weekend'])) checked @endif>
                                            <label for="sat">Saturday</label>&emsp;

                                            <input type="checkbox" id="sun" name="weekend[]" value="Sun" @if(in_array("Sun", $data['weekend'])) checked @endif>
                                            <label for="sun">Sunday</label>&emsp;

                                            <input type="checkbox" id="mon" name="weekend[]" value="Mon" @if(in_array("Mon", $data['weekend'])) checked @endif>
                                            <label for="mon">Monday</label>&emsp;

                                            <input type="checkbox" id="tue" name="weekend[]" value="Tue" @if(in_array("Tue", $data['weekend'])) checked @endif>
                                            <label for="tue">Tuesday</label>&emsp;

                                            <input type="checkbox" id="wed" name="weekend[]" value="Wed" @if(in_array("Wed", $data['weekend'])) checked @endif>
                                            <label for="wed">Wednesday</label>&emsp;

                                            <input type="checkbox" id="thurs" name="weekend[]" value="Thu" @if(in_array("Thu", $data['weekend'])) checked @endif>
                                            <label for="thurs">Thursday</label>&emsp;

                                            @if ($errors->has('weekend'))
                                                <span class="help-block">{{ $errors->first('weekend') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="control-label col-sm-2">Salary Structure:</label>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group{{ $errors->has('basic_salary') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3" for="basic_salary">Basic Salary:</label>
                                        <div class="col-sm-2">
                                            <input type="number" step="any" min="0" class="form-control" name="salary_structure[basic_salary]" id="basic_salary" required value="{{ isset($data['salary_structure']['basic_salary']) ? $data['salary_structure']['basic_salary'] : ''}}" />

                                            @if ($errors->has('basic_salary'))
                                                <span class="help-block">{{ $errors->first('basic_salary') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('house_rent') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3" for="house_rent">House Rent:</label>
                                        <div class="col-sm-2">
                                            <input type="number" step="any" min="0" class="form-control" name="salary_structure[house_rent]" id="house_rent" value="{{ isset($data['salary_structure']['house_rent']) ? $data['salary_structure']['house_rent'] : ''}}" />

                                            @if ($errors->has('house_rent'))
                                                <span class="help-block">{{ $errors->first('house_rent') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('medical_allowance') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3" for="medical_allowance">Medical Allowance:</label>
                                        <div class="col-sm-2">
                                            <input type="number" step="any" min="0" class="form-control" name="salary_structure[medical_allowance]" id="medical_allowance" value="{{ isset($data['salary_structure']['medical_allowance']) ? $data['salary_structure']['medical_allowance'] : ''}}" />

                                            @if ($errors->has('medical_allowance'))
                                                <span class="help-block">{{ $errors->first('medical_allowance') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('conveyance_allowance') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3" for="conveyance_allowance">Conveyance Allowance:</label>
                                        <div class="col-sm-2">
                                            <input type="number" step="any" min="0" class="form-control" name="salary_structure[conveyance_allowance]" id="conveyance_allowance" value="{{ isset($data['salary_structure']['conveyance_allowance']) ? $data['salary_structure']['conveyance_allowance'] : ''}}" />

                                            @if ($errors->has('conveyance_allowance'))
                                                <span class="help-block">{{ $errors->first('conveyance_allowance') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('entertainment_allowance') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3" for="entertainment_allowance">Entertainment Allowance:</label>
                                        <div class="col-sm-2">
                                            <input type="number" step="any" min="0" class="form-control" name="salary_structure[entertainment_allowance]" id="entertainment_allowance" value="{{ isset($data['salary_structure']['entertainment_allowance']) ? $data['salary_structure']['entertainment_allowance'] : ''}}" />

                                            @if ($errors->has('entertainment_allowance'))
                                                <span class="help-block">{{ $errors->first('entertainment_allowance') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('other_allowance') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 " for="other_allowance">Other Allowance:</label>
                                        <div class="col-sm-2">
                                            <input type="number" step="any" min="0" class="form-control" name="salary_structure[other_allowance]" id="other_allowance" value="{{ isset($data['salary_structure']['other_allowance']) ? $data['salary_structure']['other_allowance'] : ''}}" />

                                            @if ($errors->has('other_allowance'))
                                                <span class="help-block">{{ $errors->first('other_allowance') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('income_tax') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 " for="income_tax">Income Tax (-):</label>
                                        <div class="col-sm-2">
                                            <input type="number" step="any" min="0" class="form-control" name="salary_structure[income_tax]" id="income_tax" value="{{ isset($data['salary_structure']['income_tax']) ? $data['salary_structure']['income_tax'] : ''}}" />

                                            @if ($errors->has('income_tax'))
                                                <span class="help-block">{{ $errors->first('income_tax') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group text-center">
                                        <button type="submit" class="btn btn-sm btn-primary"> <i class="fa fa-save"></i> Save</button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
