@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Employee List
                    </a>
                </li>
                @can('add hr_employee')
                <li {{ isset($create) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.employee.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Employee
                    </a>
                </li>
                @endcan
                <li>
                    <a href="{{ route('oshnisoft-hrm.employee.export') . qString() }}">
                        <i class="fa fa-download" aria-hidden="true"></i> Export Employee
                    </a>
                </li>

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Employee
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Employee Details
                        </a>
                    </li>
                @endif

                @if (isset($employment))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Employment Status
                        </a>
                    </li>
                @endif

                @if (isset($salary))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Employee Salary
                        </a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <h3 style="width:70%;padding:5px;border-bottom:1px solid gray;">Basic Information</h3>
                            <table class="table table-bordered">

                                <tr>
                                    <th style="width:200px;">Photo</th>
                                    <th style="width:10px;">:</th>
                                    <td>{!! viewImg('employees', $data->image, ['style' => 'width:200px;']) !!}</td>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <th>:</th>
                                    <td>{{ $data->employee_no }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <th>:</th>
                                    <td>{{ $data->name }}</td>
                                </tr>
                                <tr>
                                    <th>Father Name</th>
                                    <th>:</th>
                                    <td>{{ $data->father_name }}</td>
                                </tr>

                                <tr>
                                    <th>Mother Name</th>
                                    <th>:</th>
                                    <td>{{ $data->mother_name }}</td>
                                </tr>
                                <tr>
                                    <th>Contact No</th>
                                    <th>:</th>
                                    <td>{{ $data->contact_no }}</td>
                                </tr>

                                <tr>
                                    <th>Gender</th>
                                    <th>:</th>
                                    <td>{{ $data->gender }}</td>
                                </tr>

                                <tr>
                                    <th>Nationality</th>
                                    <th>:</th>
                                    <td>{{ $data->nationality }}</td>
                                </tr>

                                <tr>
                                    <th>Religion</th>
                                    <th>:</th>
                                    <td>{{ $data->nationality }}</td>
                                </tr>

                                <tr>
                                    <th>Blood Group</th>
                                    <th>:</th>
                                    <td>{{ $data->blood_group }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <th>:</th>
                                    <td>{{ $data->email }}</td>
                                </tr>
                                <tr>
                                    <th>Present Address</th>
                                    <th>:</th>
                                    <td>{{ $data->present_address }}</td>
                                </tr>
                                <tr>
                                    <th>Permanent Address</th>
                                    <th>:</th>
                                    <td>{{ $data->permanent_address }}</td>
                                </tr>
                                <tr>
                                    <th>National ID</th>
                                    <th>:</th>
                                    <td>{{ $data->nid }}</td>
                                </tr>
                                <tr>
                                    <th>National ID Image</th>
                                    <th>:</th>
                                    <td>{!! viewImg('employees', $data->nid_front_image, ['style' => 'width:200px;']) !!} | {!! viewImg('employees', $data->nid_back_image, ['style' => 'width:200px;']) !!}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>{{ $data->status }}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <th>:</th>
                                    <td>{{ isset($data->createdBy) ?  $data->createdBy->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <th>:</th>
                                    <td>{{ isset($data->updatedBy) ?  $data->updatedBy->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Salary</th>
                                    <th>:</th>
                                    <td>{{ isset($data->salary) ?  $data->salary->gross_salary : '0' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="box-body table-responsive">
                            <h3 style="width:70%;padding:5px;border-bottom:1px solid gray;">Educational Qualification</h3>
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:120px;">Degree</th>
                                        <th>Institute</th>
                                        <th>University/Board</th>
                                        <th>Major</th>
                                        <th>Result</th>
                                        <th>Passing Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->educations as $education)
                                    <tr>
                                    <td>{{ $education->degree }}</td>
                                    <td>{{ $education->institution }}</td>
                                    <td>{{ $education->board_university }}</td>
                                    <td>{{ $education->group_subject }}</td>
                                    <td>{{ $education->result }}</td>
                                    <td>{{ $education->passing_year }}</td>
                                </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="box-body table-responsive">
                            <h3 style="width:70%;padding:5px;border-bottom:1px solid gray;">Work Experiences</h3>
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:120px;">Organization</th>
                                        <th>Role</th>
                                        <th>Responsibility</th>
                                        <th>Joining Date</th>
                                        <th>Last Working Date</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->workExperiences as $experience)
                                    <tr>
                                    <td>{{ $experience->organization }}</td>
                                    <td>{{ $experience->role }}</td>
                                    <td>{{ $experience->responsibility }}</td>
                                    <td>{{ $experience->joining_date }}</td>
                                    <td>{{ $experience->last_working_date }}</td>
                                    <td>{{ $experience->duration }}</td>
                                </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="box-body table-responsive">
                            <h3 style="width:70%;padding:5px;border-bottom:1px solid gray;">Employment History</h3>
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:120px;">Department</th>
                                        <th>Designation</th>
                                        <th>Work Station</th>
                                        <th>Supervisor</th>
                                        <th>Status</th>
                                        <th>Started At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->employments as $employment)
                                    <tr>
                                    <td>{{ $employment->department->name }}</td>
                                    <td>{{ $employment->designation->name }}</td>
                                    <td>{{ $employment->workStation->name }}</td>
                                    <td>{{ isset($employment->supervisor)? $employment->supervisor->name : '' }}</td>
                                    <td>{{ $employment->status }}</td>
                                    <td>{{ $employment->effect_date }}</td>
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
                                action="{{ isset($edit) ? route('oshnisoft-hrm.employee.update', $edit) : route('oshnisoft-hrm.employee.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">
                                        <h3 style="width:100%;padding:5px;border-bottom:1px solid gray;">Basic Information</h3>

                                        <div class="form-group{{ $errors->has('employee_no') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Staff ID:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="employee_no"
                                                value="{{ old('employee_no', isset($data) ? $data->employee_no : '') }}" required>

                                                @if ($errors->has('employee_no'))
                                                    <span class="help-block">{{ $errors->first('employee_no') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="name" required
                                                value="{{ old('name', isset($data) ? $data->name : '') }}">

                                                @if ($errors->has('name'))
                                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('father_name') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Father Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="father_name" required
                                                value="{{ old('father_name', isset($data) ? $data->father_name : '') }}">

                                                @if ($errors->has('father_name'))
                                                    <span class="help-block">{{ $errors->first('father_name') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('mother_name') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Mother Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="mother_name" required
                                                value="{{ old('mother_name', isset($data) ? $data->mother_name : '') }}">

                                                @if ($errors->has('mother_name'))
                                                    <span class="help-block">{{ $errors->first('mother_name') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="{{ $errors->has('birth_date') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-3 ">Birth Date:</label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control datepicker" name="birth_date"
                                                    value="{{ old('birth_date', isset($data) ? $data->birth_date : date('Y-m-d')) }}">

                                                    @if ($errors->has('birth_date'))
                                                        <span class="help-block">{{ $errors->first('birth_date') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="{{ $errors->has('gender') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-2 required">Gender:</label>
                                                <div class="col-sm-4">
                                                    <input type="radio" name="gender" value="Male" id="male" @if(isset($data) && $data->gender == 'Male') checked @endif> <label
                                                        for="male" style="font-weight: normal;">Male</label>

                                                    <input type="radio" name="gender" value="Female" id="female" @if(isset($data) && $data->gender == 'Female') checked @endif> <label
                                                        for="female" style="font-weight: normal;">Female</label>

                                                    <input type="radio" name="gender" value="Other" id="others" @if(isset($data) && $data->gender == 'Other') checked @endif> <label
                                                        for="others" style="font-weight: normal;">Other</label>

                                                    @if ($errors->has('gender'))
                                                        <span class="help-block">{{ $errors->first('gender') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="{{ $errors->has('nationality') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-3 required">Nationality:</label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control" name="nationality" required
                                                    value="{{ old('nationality', isset($data) ? $data->nationality : '') }}">

                                                    @if ($errors->has('nationality'))
                                                        <span class="help-block">{{ $errors->first('nationality') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="{{ $errors->has('religion') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-3 required">Religion:</label>
                                                <div class="col-sm-3">
                                                    <select class="form-control select2" name="religion" id="religion">
                                                        <option value="">Select</option>
                                                        @php
                                                        $religion = old('religion', isset($data) ? $data->religion : '')
                                                        @endphp

                                                        @foreach ( ['Islam', 'Hinduism', 'Christian', 'Buddhism'] as $relg)
                                                            <option value="{{ $relg }}"
                                                                {{ $religion == $relg ? 'selected' : '' }}>{{ $relg }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @if ($errors->has('religion'))
                                                        <span class="help-block">{{ $errors->first('religion') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="{{ $errors->has('org_joining_date') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-3 ">Joining Date:</label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control datepicker" name="org_joining_date"
                                                       value="{{ old('org_joining_date', isset($data) ? $data->org_joining_date : '') }}">

                                                    @if ($errors->has('org_joining_date'))
                                                        <span class="help-block">{{ $errors->first('org_joining_date') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="{{ $errors->has('blood_group') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-3 ">Blood Group:</label>
                                                <div class="col-sm-3">
                                                    <select name="blood_group" class="form-control" required>
                                                        @php
                                                            $blood_group = old('blood_group', isset($data) ? $data->blood_group : '')
                                                        @endphp
                                                        @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bloodGroup)
                                                            <option value="{{ $bloodGroup }}"
                                                                {{ $blood_group == $bloodGroup ? 'selected' : '' }}>{{ $bloodGroup }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @if ($errors->has('blood_group'))
                                                        <span class="help-block">{{ $errors->first('blood_group') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('contact_no') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 ">Contact Number:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="contact_no"
                                                value="{{ old('contact_no', isset($data) ? $data->contact_no : '') }}">

                                                @if ($errors->has('contact_no'))
                                                    <span class="help-block">{{ $errors->first('contact_no') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Email:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="email"
                                                value="{{ old('email', isset($data) ? $data->email : '') }}" >

                                                @if ($errors->has('email'))
                                                    <span class="help-block">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('present_address') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Present Address:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="present_address" required
                                                value="{{ old('present_address', isset($data) ? $data->present_address : '') }}" >

                                                @if ($errors->has('present_address'))
                                                    <span class="help-block">{{ $errors->first('present_address') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('permanent_address') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 ">Permanent Address:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="permanent_address"
                                                value="{{ old('permanent_address', isset($data) ? $data->permanent_address : '') }}">

                                                @if ($errors->has('permanent_address'))
                                                    <span class="help-block">{{ $errors->first('permanent_address') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Select Image :</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="image" accept="image/*">
                                                @if(isset($data->image))
                                                <img src="{{ asset('storage/employees/thumb/'.$data->image) }}" alt="{{ $data->image }}" class="img-thumbnail">
                                                @endif
                                                @if ($errors->has('image'))
                                                    <span class="help-block">{{ $errors->first('image') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('nid_no') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">NID No:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="nid_no">

                                                @if ($errors->has('nid_no'))
                                                    <span class="help-block">{{ $errors->first('nid_no') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('nid_front_image') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">NID Front :</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="nid_front_image"
                                                    accept="image/*">
                                                    @if(isset($data->nid_front_image))
                                                    <img src="{{ asset('storage/employees/thumb/'.$data->nid_front_image) }}" alt="{{ $data->nid_front_image }}" class="img-thumbnail">
                                                    @endif
                                                @if ($errors->has('nid_front_image'))
                                                    <span class="help-block">{{ $errors->first('nid_front_image') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('nid_back_image') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">NID Back :</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" name="nid_back_image" accept="image/*">
                                                @if(isset($data->nid_back_image))
                                                <img src="{{ asset('storage/employees/thumb/'.$data->nid_back_image) }}" alt="{{ $data->nid_back_image }}" class="img-thumbnail">
                                                @endif
                                                @if ($errors->has('nid_back_image'))
                                                    <span class="help-block">{{ $errors->first('nid_back_image') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Status:</label>
                                            <div class="col-sm-9">
                                                <select name="status" class="form-control select2" required>
                                                    @php
                                                    $status = old('status', isset($data) ? $data->status : '')
                                                    @endphp
                                                    @foreach (['Active', 'Deactivated'] as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('status'))
                                                    <span class="help-block">{{ $errors->first('status') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-sm-12">
                                        {{-- Education Block --}}
                                        <h3 style="width:70%;padding:5px;border-bottom:1px solid gray;">Education</h3>

                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width:120px;">Degree</th>
                                                    <th>Institute</th>
                                                    <th>University/Board</th>
                                                    <th  style="width:120px;">Major</th>
                                                    <th  style="width:120px;">Result</th>
                                                    <th  style="width:120px;">Passing Year</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>

                                            <tbody id="output">
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="education_id[]" value="" />

                                                        <input type="text" name="degree[]" id="degree" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="institution[]" id="institution" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="board_university[]" id="board_university" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="group_subject[]" id="group_subject" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="result[]" id="result" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="passing_year[]" id="passing_year" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-success btn-sm" onclick="add_education()">+</button>
                                                    </td>
                                                </tr>

                                            @php $row_id = 1; @endphp
                                            @if(isset($edit))
                                            @foreach($data->educations as $value)
                                                <tr id="row{{$row_id}}">
                                                    <td>
                                                        <input type="hidden" name="education_id[]" value="{{ $value->id }}" />
                                                        <input type="text" name="degree[]" class="form-control" value="{{ $value->degree }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="institution[]" class="form-control" value="{{ $value->institution }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="board_university[]" class="form-control" value="{{ $value->board_university }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="group_subject[]" class="form-control" value="{{ $value->group_subject }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="result[]" class="form-control" value="{{ $value->result }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="passing_year[]" class="form-control" value="{{ $value->passing_year }}" />
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeEducation({{$row_id}})">-</button>
                                                    </td>
                                                </tr>

                                                @php $row_id++; @endphp
                                            @endforeach
                                            <input type="hidden" id="row_id" value="{{ $row_id }}" />
                                            @endif
                                            </tbody>
                                        </table>


                                        {{-- End Education Block --}}
                                    </div>

                                    <div class="col-sm-12">
                                        {{-- Experience Block --}}
                                        <h3 style="width:70%;padding:5px;border-bottom:1px solid gray;">Experience</h3>

                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width:120px;">Organization</th>
                                                    <th>Role</th>
                                                    <th>Responsibility</th>
                                                    <th  style="width:120px;">Joining Date</th>
                                                    <th  style="width:120px;">Last Working Date</th>
                                                    <th  style="width:120px;">Duration</th>
                                                </tr>
                                            </thead>

                                            <tbody id="experience_output">
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="experience_id[]" value="" />

                                                        <input type="text" name="organization[]" id="organization" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="role[]" id="role" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="responsibility[]" id="responsibility" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="joining_date[]" id="joining_date" class="form-control datepicker" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="last_working_date[]" id="last_working_date" class="form-control datepicker" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="duration[]" id="duration" class="form-control" />
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-success btn-sm" onclick="addExperience()">+</button>
                                                    </td>
                                                </tr>

                                            @php $experience_row_id = 1; @endphp
                                            @if(isset($edit))
                                            @foreach($data->workExperiences as $value)
                                                <tr id="experience_row{{$experience_row_id}}">
                                                    <td>
                                                        <input type="hidden" name="experience_id[]" value="{{ $value->id }}" />
                                                        <input type="text" name="organization[]" class="form-control" value="{{ $value->organization }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="role[]" class="form-control" value="{{ $value->role }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="responsibility[]" class="form-control" value="{{ $value->responsibility }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="joining_date[]" class="form-control datepicker" value="{{ $value->joining_date }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="last_working_date[]" class="form-control datepicker" value="{{ $value->last_working_date }}" />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="duration[]" class="form-control" value="{{ $value->duration }}" />
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeExperience({{$experience_row_id}})">-</button>
                                                    </td>
                                                </tr>

                                                @php $experience_row_id++; @endphp
                                            @endforeach
                                            <input type="hidden" id="experience_row_id" value="{{ $experience_row_id }}" />
                                            @endif
                                            </tbody>
                                        </table>

                                        <div class="form-group">
                                            <div class="col-sm-offset-3 text-center">
                                                <button type="submit"
                                                    class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                                <button type="reset"
                                                    class="btn btn-warning btn-flat">Clear</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif(isset($employment))
                <div class="tab-pane active">
                    <div class="box-body">
                        <form method="POST"
                            action="{{ route('oshnisoft-hrm.employee.update-employment', $employment) }}{{ qString() }}"
                            id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                            @csrf

                            @if (isset($esEdit))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-sm-8">

                                    {{-- End Experience Block --}}

                                    <h3 style="width:100%;padding:5px;border-bottom:1px solid gray;">Employment of {{ $employeeData->name . ' [' . $employeeData->employee_no . ']'}}</h3>

                                    <div class="form-group">
                                        <div class="{{ $errors->has('department_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Department:</label>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="department_id" required>
                                                    <option value="">Select</option>
                                                    @php
                                                    $department_id = old('department_id', isset($data) ? $data->department_id : '')
                                                    @endphp
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}" {{ $department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('department_id'))
                                                    <span class="help-block">{{ $errors->first('department_id') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="{{ $errors->has('designation_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Designation:</label>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="designation_id" id="designation_id">
                                                    <option value="">Select</option>
                                                    @php
                                                    $designation_id = old('designation_id', isset($data) ? $data->designation_id : '')
                                                    @endphp
                                                    @foreach ($designations as $designation)
                                                    <option value="{{ $designation->id }}" {{ $designation_id == $designation->id ? 'selected' : '' }}>{{ $designation->name }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('designation_id'))
                                                    <span class="help-block">{{ $errors->first('designation_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="{{ $errors->has('work_station_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Working Location:</label>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="work_station_id" required>
                                                    <option value="">Select</option>
                                                    @php
                                                    $work_station_id = old('work_station_id', isset($data) ? $data->work_station_id : '')
                                                    @endphp
                                                    @foreach ($workstations as $workstation)
                                                        <option value="{{ $workstation->id }}"
                                                            {{ $work_station_id == $workstation->id ? 'selected' : '' }}>{{ $workstation->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('work_station_id'))
                                                    <span
                                                        class="help-block">{{ $errors->first('work_station_id') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="{{ $errors->has('supervisor_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 ">Supervisor:</label>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="supervisor_id" id="supervisor_id">
                                                    <option value="">Select</option>
                                                    @foreach ($employees as $employee)
                                                    @php
                                                    $supervisor_id = old('supervisor_id', isset($data) ? $data->supervisor_id : '')
                                                    @endphp
                                                    @if($employee->id != $employeeData->id)
                                                        <option value="{{ $employee->id }}" {{ $supervisor_id == $employee->id ? 'selected' : '' }}>{{ $employee->name }}
                                                        </option>
                                                    @endif
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('supervisor_id'))
                                                    <span class="help-block">{{ $errors->first('supervisor_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="{{ $errors->has('status') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Status:</label>
                                            <div class="col-sm-3">
                                                <select id="status" name="status" class="form-control select2" required onchange="checkProbation()">
                                                    @php
                                                    $status = old('status', isset($data) ? $data->status : '')
                                                    @endphp
                                                    @foreach (['Probation', 'Confirm', 'Terminated'] as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('status'))
                                                    <span class="help-block">{{ $errors->first('status') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div id="probation_end_on" class="probation {{ $errors->has('probation_end_on') ? ' has-error' : '' }}" id="probation_end_on">
                                            <label class="control-label col-sm-3 required">Probation End On:</label>
                                            <div class="col-sm-3">
                                                <input type="text"  id="probation_end_on_val" name="probation_end_on" class="form-control datepicker" value="{{ isset($data) ? $data->probation_end_on : date('Y-m-d') }}" />
                                                @if ($errors->has('probation_end_on'))
                                                    <span class="help-block">{{ $errors->first('probation_end_on') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('remarks') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 ">Remarks:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="remarks">

                                            @if ($errors->has('remarks'))
                                                <span class="help-block">{{ $errors->first('remarks') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-3 text-center">
                                            <button type="submit"
                                                class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                            <button type="reset"
                                                class="btn btn-warning btn-flat">Clear</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @elseif(isset($salary))
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <form method="POST" action="{{ route('oshnisoft-hrm.employee.salary-update', $salary) }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-8" style="margin-top: 10px;">

                                        <div class="form-group">
                                            <label class="control-label col-sm-2">Name:</label>
                                            <div class="col-sm-4">
                                                {{ $data->name }}
                                            </div>

                                            <label class="control-label col-sm-2">Department:</label>
                                            <div class="col-sm-4">
                                                {{ $data->employmentStatus->department->name ?? '' }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2">Joining Date:</label>
                                            <div class="col-sm-4">
                                                {{ $data->org_joining_date }}
                                            </div>

                                            <label class="control-label col-sm-2">Designation:</label>
                                            <div class="col-sm-4">
                                                {{ $data->employmentStatus->designation->name ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 15px;">
                                    <div class="col-sm-8">
                                        <div class="form-group{{ $errors->has('gross_salary') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4 required">Gross Salary:</label>
                                            <div class="col-sm-3">
                                                <input type="number" step="any" min="0" class="form-control" name="gross_salary" id="gross_salary" onchange="calculateGrossSalary()" onkeyup="calculateGrossSalary()"  value="{{ isset($salaryData->gross_salary) ? $salaryData->gross_salary : ''}}" />

                                                @if ($errors->has('gross_salary'))
                                                    <span class="help-block">{{ $errors->first('gross_salary') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('basic_salary') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4 required">Basic Salary:</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="basic_salary" id="basic_salary" value="{{ isset($salaryData->basic_salary) ? $salaryData->basic_salary : ''}}" />

                                                <input type="hidden" id="basic_salary_percent" value="{{ isset($salaryStructure['basic_salary']) ? $salaryStructure['basic_salary'] : 0 }}" />

                                                @if ($errors->has('basic_salary'))
                                                    <span class="help-block">{{ $errors->first('basic_salary') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('house_rent') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">House Rent:</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="house_rent" id="house_rent" value="{{ isset($salaryData->house_rent) ? $salaryData->house_rent : ''}}" />

                                                <input type="hidden" id="house_rent_percent" value="{{ isset($salaryStructure['house_rent']) ? $salaryStructure['house_rent'] : 0 }}" />

                                                @if ($errors->has('house_rent'))
                                                    <span class="help-block">{{ $errors->first('house_rent') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('medical_allowance') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Medical Allowance:</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="medical_allowance" id="medical_allowance" value="{{ isset($salaryData->medical_allowance) ? $salaryData->medical_allowance : ''}}"  />

                                                <input type="hidden" id="medical_allowance_percent" value="{{ isset($salaryStructure['medical_allowance']) ? $salaryStructure['medical_allowance'] : 0 }}" />

                                                @if ($errors->has('medical_allowance'))
                                                    <span class="help-block">{{ $errors->first('medical_allowance') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('conveyance_allowance') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Conveyance Allowance:</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="conveyance_allowance" id="conveyance_allowance" value="{{ isset($salaryData->conveyance_allowance) ? $salaryData->conveyance_allowance : ''}}"  />

                                                <input type="hidden" id="conveyance_allowance_percent" value="{{ isset($salaryStructure['conveyance_allowance']) ? $salaryStructure['conveyance_allowance'] : 0 }}" />

                                                @if ($errors->has('conveyance_allowance'))
                                                    <span class="help-block">{{ $errors->first('conveyance_allowance') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('entertainment_allowance') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Entertainment Allowance:</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="entertainment_allowance" id="entertainment_allowance" value="{{ isset($salaryData->entertainment_allowance) ? $salaryData->entertainment_allowance : ''}}"  />

                                                <input type="hidden" id="entertainment_allowance_percent" value="{{ isset($salaryStructure['entertainment_allowance']) ? $salaryStructure['entertainment_allowance'] : 0 }}" />

                                                @if ($errors->has('entertainment_allowance'))
                                                    <span class="help-block">{{ $errors->first('entertainment_allowance') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('other_allowance') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4 ">Other Allowance:</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="other_allowance" id="other_allowance" value="{{ isset($salaryData->other_allowance) ? $salaryData->other_allowance : ''}}"  />

                                                <input type="hidden" id="other_allowance_percent" value="{{ isset($salaryStructure['other_allowance']) ? $salaryStructure['other_allowance'] : 0 }}" />

                                                @if ($errors->has('other_allowance'))
                                                    <span class="help-block">{{ $errors->first('other_allowance') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('income_tax') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4 ">Income Tax (-):</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="income_tax" id="income_tax" value="{{ isset($salaryData->income_tax) ? $salaryData->income_tax : ''}}" />

                                                <input type="hidden" id="income_tax_percent" value="{{ isset($salaryStructure['income_tax']) ? $salaryStructure['income_tax'] : 0 }}" />

                                                @if ($errors->has('income_tax'))
                                                    <span class="help-block">{{ $errors->first('income_tax') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('pf_deduction') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-4">Provident Fund Deduction:</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="pf_deduction" id="pf_deduction" value="{{ isset($salaryData->pf_deduction) ? $salaryData->pf_deduction : ''}}" required/>

                                                @if ($errors->has('pf_deduction'))
                                                    <span class="help-block">{{ $errors->first('pf_deduction') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('mobile_bill') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-4">Phone Bill:</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" name="mobile_bill" id="mobile_bill" value="{{ isset($salaryData->mobile_bill) ? $salaryData->mobile_bill : ''}}" required/>

                                                @if ($errors->has('mobile_bill'))
                                                    <span class="help-block">{{ $errors->first('mobile_bill') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('bank_acc_no') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Bank Acc No.:</label>
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" name="bank_acc_no" id="bank_acc_no" value="{{ isset($salaryData->bank_acc_no) ? $salaryData->bank_acc_no : ''}}" />

                                                @if ($errors->has('bank_acc_no'))
                                                    <span class="help-block">{{ $errors->first('bank_acc_no') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('bank_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-4">Bank Name:</label>
                                            <div class="col-sm-5">
                                                <select type="text" class="form-control" name="bank_id" id="bank_id">
                                                    <option value="">Select Bank Name</option>

                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank->id }}"  {{ isset($salaryData->bank_id) ? $bank->id == $salaryData->bank_id ? 'selected' : '' : '' }}>
                                                        {{ $bank->bank_name }}
                                                    </option>
                                                @endforeach

                                                </select>

                                                @if ($errors->has('bank_id'))
                                                    <span class="help-block">{{ $errors->first('bank_id') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <div class="col-sm-offset-3 text-center">
                                                <button type="submit"
                                                    class="btn btn-success btn-flat">{{ __('Save') }}</button>
                                                <button type="reset"
                                                    class="btn btn-warning btn-flat">{{ __('Clear') }}</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('oshnisoft-hrm.employee.index') }}" class="form-inline">
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
                                        <button type="submit"
                                            class="btn btn-info btn-flat">Search</button>
                                        <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.employee.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>Joining Date</th>
                                        <th>Mobile No</th>
                                        <th>Present Address</th>
                                        <th>Salary</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $val)
                                        <tr style="background-color: {{ $val->status == 'Deactivated' ? 'red' : '' }}">
                                            <td>{{ $val->employee_no }}</td>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ isset($val->employmentStatus) ? $val->employmentStatus->department->name : 'Not Set'}}</td>
                                            <td>{{ isset($val->employmentStatus) ? $val->employmentStatus->designation->name : 'Not Set'}}</td>
                                            <td>{{ $val->org_joining_date }}</td>
                                            <td>{{ $val->contact_no }}</td>
                                            <td>{{ $val->present_address }}</td>
                                            <td>{{ isset($val->salary) ?  $val->salary->gross_salary : 'Not Set' }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button"
                                                        data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show hr_employee')
                                                        <li>
                                                            <a href="{{ route('oshnisoft-hrm.employee.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a>
                                                        </li>
                                                        @endcan

                                                        @can('edit hr_employee')
                                                        <li><a href="{{ route('oshnisoft-hrm.employee.edit', $val->id) . qString() }}"><i class="fa fa-edit"></i> Edit</a>
                                                        </li>
                                                        @endcan

                                                        @can('edit hr_employee')
                                                        <li><a href="{{ route('oshnisoft-hrm.employee.salary', $val->id) . qString() }}"><i class="fa fa-pencil"></i> {{ isset($val->salary) ? 'Update Salary' : 'Assign Salary' }}</a></li>
                                                        @endcan

                                                        @can('edit hr_employee')
                                                        <li><a href="{{ route('oshnisoft-hrm.employee.employment', $val->id) . qString() }}"><i class="fa fa-pencil"></i> {{ isset($val->employment) ? 'Update Employment' : 'Submit Employment' }}</a></li>
                                                        @endcan

                                                        @can('edit hr_employee')
                                                        <li><a onclick="activity('{{ route('oshnisoft-hrm.employee.status', $val->id) . qString() }}')"><i class="fa fa-pencil"></i> {{ $val->status == 'Active' ? 'Deactivated' : 'Active' }}</a></li>
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
        var status = $('#status').val();
            if(status != 'Probation') {
                $('#probation_end_on').hide();
                $('#probation_end_on_val').val('');

            } else {
                $('#probation_end_on').show();
                let val = $('#probation_end_on_val').val();
                $('#probation_end_on_val').val(val);
            }
    });

        function checkProbation(){
            var status = $('#status').val();
            if(status != 'Probation') {
                $('#probation_end_on').hide();
                $('#probation_end_on_val').val('');
            } else {
                $('#probation_end_on').show();
                let val = $('#probation_end_on_val').val();
                $('#probation_end_on_val').val(val);
            }

        }

        var row_id = $("#row_id").val() || 1;
        function add_education() {
            let output = '';

            let degree = document.getElementById('degree').value;
            let institution = document.getElementById('institution').value;
            let board_university = document.getElementById('board_university').value;
            let group_subject = document.getElementById('group_subject').value;
            let result = document.getElementById('result').value;
            let passing_year = document.getElementById('passing_year').value;

            // if nothing added then don't add new row
            if(degree == '' || institution == '' || board_university == '' || group_subject == '' || result == '' || passing_year == ''){ return; }

            output += '<tr id="row'+row_id+'">';

            output += '<td><input type="hidden" name="education_id[]" value="" /><input type="text" class="form-control" name="degree[]" value="'+degree+'" /></td>';

            output += '<td><input type="text" class="form-control" name="institution[]" value="'+institution+'" /></td>';

            output += '<td><input type="text" class="form-control" name="board_university[]" value="'+board_university+'" /></td>';

            output += '<td><input type="text" class="form-control" name="group_subject[]" value="'+group_subject+'" /></td>';

            output += '<td><input type="text" class="form-control" name="result[]" value="'+result+'" /></td>';

            output += '<td><input type="text" class="form-control" name="passing_year[]" value="'+passing_year+'" /></td>';

            output += '<td><button class="btn btn-sm btn-danger" onclick="removeEducation('+row_id+')">-</button></td>';

            output += '</tr>';

            $("#output").append(output);

            row_id++;

            resetEducation();
        }
        function resetEducation(){
            document.getElementById('degree').value = '';
            document.getElementById('institution').value = '';
            document.getElementById('board_university').value = '';
            document.getElementById('group_subject').value = '';
            document.getElementById('result').value = '';
            document.getElementById('passing_year').value = '';
        }
        function removeEducation(index){
            $("#row"+index).remove();
        }

        var experience_row_id = $("#experience_row_id").val() || 1;
        function addExperience() {
            let output = '';

            let organization = document.getElementById('organization').value;
            let role = document.getElementById('role').value;
            let responsibility = document.getElementById('responsibility').value;
            let joining_date = document.getElementById('joining_date').value;
            let last_working_date = document.getElementById('last_working_date').value;
            let duration = document.getElementById('duration').value;

            // if nothing added then don't add new row
            if(organization == '' || role == '' || responsibility == '' || joining_date == '' || last_working_date == '' || duration == ''){ return; }

            output += '<tr id="experience_row'+experience_row_id+'">';

            output += '<td><input type="hidden" name="experience_id[]" value="" /><input type="text" class="form-control" name="experience_degree[]" value="'+organization+'" /></td>';

            output += '<td><input type="text" class="form-control" name="role[]" value="'+role+'" /></td>';

            output += '<td><input type="text" class="form-control" name="responsibility[]" value="'+responsibility+'" /></td>';

            output += '<td><input type="text" class="form-control" name="joining_date[]" value="'+joining_date+'" /></td>';

            output += '<td><input type="text" class="form-control" name="last_working_date[]" value="'+last_working_date+'" /></td>';

            output += '<td><input type="text" class="form-control" name="duration[]" value="'+duration+'" /></td>';

            output += '<td><button class="btn btn-sm btn-danger" onclick="removeExperience('+experience_row_id+')">-</button></td>';

            output += '</tr>';

            $("#experience_output").append(output);

            experience_row_id++;

            resetExperience();
        }
        function resetExperience(){
            document.getElementById('organization').value = '';
            document.getElementById('role').value = '';
            document.getElementById('responsibility').value = '';
            document.getElementById('joining_date').value = '';
            document.getElementById('last_working_date').value = '';
            document.getElementById('duration').value = '';
        }
        function removeExperience(index){
            $("#experience_row"+index).remove();
        }

        function calculateGrossSalary(){
            let gross_salary = parseInt($("#gross_salary").val()) || 0;

            let basic_salary_percent = parseInt($("#basic_salary_percent").val()) || 0;
            let house_rent_percent = parseInt($("#house_rent_percent").val()) || 0;
            let medical_allowance_percent = parseInt($("#medical_allowance_percent").val()) || 0;

            let conveyance_allowance_percent = parseInt($("#conveyance_allowance_percent").val()) || 0;

            let entertainment_allowance_percent = parseInt($("#entertainment_allowance_percent").val()) || 0;

            let other_allowance_percent = parseInt($("#other_allowance_percent").val()) || 0;

            let income_tax_percent = parseInt($("#income_tax_percent").val()) || 0;

            let basic_salary = ((gross_salary * basic_salary_percent)/100);
            let house_rent = ((gross_salary * house_rent_percent)/100);
            let medical_allowance = ((gross_salary * medical_allowance_percent)/100);
            let conveyance_allowance = ((gross_salary * conveyance_allowance_percent)/100);

            let entertainment_allowance = ((gross_salary * entertainment_allowance_percent)/100);

            let other_allowance = ((gross_salary * other_allowance_percent)/100);
            let income_tax = ((gross_salary * income_tax_percent)/100);

            $("#basic_salary").val(basic_salary);
            $("#house_rent").val(house_rent);
            $("#medical_allowance").val(medical_allowance);
            $("#conveyance_allowance").val(conveyance_allowance);
            $("#entertainment_allowance").val(entertainment_allowance);
            $("#other_allowance").val(other_allowance);
            $("#income_tax").val(income_tax);

            console.log(gross_salary, basic_salary_percent, house_rent_percent, medical_allowance_percent, conveyance_allowance_percent, entertainment_allowance_percent, other_allowance_percent, income_tax_percent);
        }

        $("#basic_salary, #house_rent, #medical_allowance, #conveyance_allowance, #entertainment_allowance, #other_allowance, #income_tax").on("change", function() {
            var basic_salary = parseInt($("#basic_salary").val()) || 0;
            var house_rent = parseInt($("#house_rent").val()) || 0;
            var medical_allowance = parseInt($("#medical_allowance").val()) || 0;
            var conveyance_allowance = parseInt($("#conveyance_allowance").val()) || 0;
            var entertainment_allowance = parseInt($("#entertainment_allowance").val()) || 0;
            var other_allowance = parseInt($("#other_allowance").val()) || 0;
            var income_tax = parseInt($("#income_tax").val()) || 0;

            var gross_salary = basic_salary + house_rent + medical_allowance + conveyance_allowance + entertainment_allowance + other_allowance - income_tax;

            $("#gross_salary").val(gross_salary);
        });
    </script>
@endpush
