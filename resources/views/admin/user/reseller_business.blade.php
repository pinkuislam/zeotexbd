@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.user.reseller_business.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Reseller Business List
                    </a>
                </li>
                @can('add reseller_business')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.user.reseller_business.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Reseller Business
                        </a>
                    </li>
                @endcan

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Reseller Business
                        </a>
                    </li>
                @endif
                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Reseller Business Details
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
                                    <th style="width:220px;">Code</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->code }}</td>
                                </tr>
                                <tr>
                                    <th style="width:220px;">Name</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <th>:</th>
                                    <td>{{ $data->mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile 2</th>
                                    <th>:</th>
                                    <td>{{ $data->mobile_2 }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <th>:</th>
                                    <td>{{ $data->email }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <th>:</th>
                                    <td>{{ $data->address }}</td>
                                </tr>
                                <tr>
                                    <th>Image</th>
                                    <th>:</th>
                                    <td>
                                        @if(isset($data->image))
                                        <img src="{{ asset('storage/users/thumb/'.$data->image) }}" alt="{{ $data->image }}" class="img-thumbnail">
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>NID NO</th>
                                    <th>:</th>
                                    <td>{{ $data->nid_no }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <th>:</th>
                                    <td>{{ $data->gender }}</td>
                                </tr>
                                <tr>
                                    <th>FB Page Link</th>
                                    <th>:</th>
                                    <td>{{ $data->fb_page_link }}</td>
                                </tr>
                                <tr>
                                    <th>Opening Due</th>
                                    <th>:</th>
                                    <td>{{ $data->opening_due }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>{{ $data->status }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.user.reseller_business.update', $edit) : route('admin.user.reseller_business.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $data->id }}">
                                @endif

                                <div class="row">
                                    <div class="col-sm-12">
                                       

                                        <div class="form-group{{ $errors->has('access_role') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Access Role</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="access_role" required>
                                                    <option value="">Select Access Role</option>
                                                    @php($access_role = old('access_role', isset($data) ? $data->getRoleNames()[0] : ''))
                                                    @foreach ($roles as $rl)
                                                        @if ($rl->name != 'Super Admin')
                                                            <option value="{{ $rl->name }}"
                                                                {{ $rl->name == $access_role ? 'selected' : '' }}>
                                                                {{ $rl->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('access_role'))
                                                    <span class="help-block">{{ $errors->first('access_role') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="{{ old('name', isset($data) ? $data->name : '') }}" required>

                                                @if ($errors->has('name'))
                                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group admin @error('mobile') has-error @enderror">
                                            <label class="control-label col-sm-3 required">Mobile:</label>

                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="mobile" id="mobile"
                                                    value="{{ old('mobile', isset($data) ? $data->mobile : '') }}" required
                                                    onblur="chkMobile(this.value)">
                                                <span id="mobileMsg" class="text-danger"></span>

                                                @error('mobile')
                                                    <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group admin @error('mobile_2') has-error @enderror">
                                            <label class="control-label col-sm-3">Mobile 2:</label>

                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="mobile_2" id="mobile_2"
                                                    value="{{ old('mobile_2', isset($data) ? $data->mobile_2 : '') }}"
                                                    onblur="chkMobile(this.value)">
                                                <span id="mobile_2Msg" class="text-danger"></span>

                                                @error('mobile_2')
                                                    <span class="help-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('fb_page_link') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">FB Page Link:</label>
                                            <div class="col-sm-9">
                                                <input type="fb_page_link" class="form-control" name="fb_page_link"
                                                    value="{{ old('fb_page_link', isset($data) ? $data->fb_page_link : '') }}">

                                                @if ($errors->has('fb_page_link'))
                                                    <span class="help-block">{{ $errors->first('fb_page_link') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Email:</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" name="email"
                                                    value="{{ old('email', isset($data) ? $data->email : '') }}" required>

                                                @if ($errors->has('email'))
                                                    <span class="help-block">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Address:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="address" name="address"
                                                    value="{{ old('address', isset($data) ? $data->address : '') }}" required>

                                                @if ($errors->has('address'))
                                                    <span class="help-block">{{ $errors->first('address') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('opening_due') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 ">Opening Due:</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" id="opening_due" name="opening_due"
                                                    value="{{ old('opening_due', isset($data) ? $data->opening_due : '') }}">

                                                @if ($errors->has('opening_due'))
                                                    <span class="help-block">{{ $errors->first('opening_due') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('nid_no') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 ">NID NO:</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" id="nid_no" name="nid_no"
                                                    value="{{ old('nid_no', isset($data) ? $data->nid_no : '') }}">

                                                @if ($errors->has('nid_no'))
                                                    <span class="help-block">{{ $errors->first('nid_no') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Gender:</label>
                                            <div class="col-sm-9">
                                                <select name="gender" class="form-control select2">
                                                    @php($gender = old('gender', isset($data) ? $data->gender : ''))
                                                    @foreach (['Male', 'Female', 'Other'] as $sts)
                                                    <option value="{{ $sts }}"
                                                    {{ $gender == $sts ? 'selected' : '' }}>{{ $sts }}
                                                </option>
                                                @endforeach
                                            </select>
                                            
                                            @if ($errors->has('gender'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('gender') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3">Select Image :</label>
                                        <div class="col-sm-9">
                                            <input type="file" class="form-control" name="image" accept="image/*">
                                            @if(isset($data->image))
                                            <img src="{{ asset('storage/users/thumb/'.$data->image) }}" alt="{{ $data->image }}" class="img-thumbnail">
                                            @endif
                                            @if ($errors->has('image'))
                                                <span class="help-block">{{ $errors->first('image') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 {!! isset($create) ? 'required' : '' !!}">Password:</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" name="password"
                                                    {{ isset($create) ? 'required' : '' }}>

                                                @if ($errors->has('password'))
                                                    <span class="help-block">{{ $errors->first('password') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3 {!! isset($create) ? 'required' : '' !!}">Confirm
                                                password:</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" name="password_confirmation"
                                                    {{ isset($create) ? 'required' : '' }}>
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Status:</label>
                                            <div class="col-sm-9">
                                                <select name="status" class="form-control select2" required>
                                                    @php($status = old('status', isset($data) ? $data->status : ''))
                                                    @foreach (['Active', 'Deactivated'] as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('status'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('status') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group text-center">
                                            <button type="submit"
                                                class="btn btn-success btn-flat btn-lg">{{ isset($edit) ? 'Update' : 'Submit' }}</button>
                                            <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.user.reseller_business.index') }}" class="form-inline">
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
                                        <button type="submit" class="btn btn-custom btn-flat">Search</button>
                                        <a class="btn btn-custom btn-flat"
                                            href="{{ route('admin.user.reseller_business.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Access Roles</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ $val->mobile }}</td>
                                            <td>{{ $val->email }}</td>
                                            <td>{{ $val->role }}</td>
                                            <td>{{ count($val->getRoleNames()) > 0 ? $val->getRoleNames()[0] : '' }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">

                                                        @can('show reseller_business')
                                                            <li><a href="{{ route('admin.user.reseller_business.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @can('edit reseller_business')
                                                            <li><a href="{{ route('admin.user.reseller_business.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan

                                                        @can('delete reseller_business')
                                                            <li><a onclick="deleted('{{ route('admin.user.reseller_business.destroy', $val->id) . qString() }}')"><i
                                                                        class="fa fa-close"></i> Delete</a></li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($records) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $records->appends(Request::except('page'))->links() }}
                            </div>

                            <div class="col-sm-4">
                                <div class="pagi-limit-box">
                                    <div class="input-group pagi-limit-box-body">
                                        <span class="input-group-addon">Show:</span>
                                        <select class="form-control pagi-limit" name="limit">
                                            @foreach (paginations() as $pag)
                                                <option value="{{ qUrl(['limit' => $pag]) }}" {{ $pag == Request::get('limit') ? 'selected' : '' }}>{{ $pag }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
