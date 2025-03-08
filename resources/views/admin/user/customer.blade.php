@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.user.customer.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Customer List
                    </a>
                </li>

                @can('add customer')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.user.customer.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Customer
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-upload" aria-hidden="true"></i> Import Customer
                        </a>
                    </li>
                @endcan

                <li>
                    <a href="{{ route('admin.user.customer.export') . qString() }}">
                        <i class="fa fa-download" aria-hidden="true"></i> Export Customer
                    </a>
                </li>

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Customer
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Customer Details
                        </a>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                    <tr>
                                        <th style="width:120px;">Type</th>
                                        <th style="width:10px;">:</th>
                                        <td>{{ $data->type }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width:120px;">{{ $data->type }}</th>
                                        <th style="width:10px;">:</th>
                                        <td>{{ $data->user->name }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th style="width:120px;">Name</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->name }}</td>
                                </tr>
                                <tr>
                                    <th style="width:120px;">Email</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->email ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Person</th>
                                    <th>:</th>
                                    <td>{{ $data->contact_name }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <th>:</th>
                                    <td>{{ $data->mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <th>:</th>
                                    <td>{{ $data->address }}</td>
                                </tr>
                                <tr>
                                    <th>Shipping Address</th>
                                    <th>:</th>
                                    <td>{{ $data->shipping_address }}</td>
                                </tr>
                                <tr>
                                    <th>Shipping Method</th>
                                    <th>:</th>
                                    <td>{{ isset($data->shipping) ? $data->shipping->name : '' }}</td>
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
                                action="{{ isset($edit) ? route('admin.user.customer.update', $edit) : route('admin.user.customer.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif
                                <input type="hidden" name="id" value="{{ old('id', isset($data) ? $data->id : '') }}">
                                <div class="row">
                                    <div class="col-sm-8">
                                        @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                            <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                                <label class="control-label col-sm-3 required">Type:</label>
                                                <div class="col-sm-9">
                                                    <select name="type" class="form-control select2" required id="type">
                                                        @php($type = old('type', isset($data) ? $data->type : ''))
                                                        @foreach (['Admin', 'Seller', 'Reseller'] as $sts)
                                                            <option value="{{ $sts }}"
                                                                {{ $type == $sts ? 'selected' : '' }}>{{ $sts }}
                                                            </option>
                                                        @endforeach
                                                    </select>
        
                                                    @if ($errors->has('type'))
                                                        <span class="help-block">{{ $errors->first('type') }}</span>
                                                    @endif
                                                </div>
                                            </div>    
                                            <div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }}" id="user" @if (isset($data)) @if ($data->type == "Admin") style="display: none" @endif   @else style="display: none" @endif>
                                                <label class="control-label col-sm-3">Seller or Reseller:</label>
                                                <div class="col-sm-9">
                                                    <select name="user_id" class="form-control select2" id="user_id">
                                                        <option value=""> Select Seller or Reseller</option>
                                                        @if (isset($data))
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}" {{ $user->id == $data->user_id ? "selected" : ""  }}> {{ $user->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
        
                                                    @if ($errors->has('user_id'))
                                                        <span class="help-block">{{ $errors->first('user_id') }}</span>
                                                    @endif
                                                </div>
                                            </div>    
                                        @endif

                                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Name:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ old('name', isset($data) ? $data->name : '') }}" required>
                                                @if ($errors->has('name'))
                                                    <span class="help-block">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 ">Contact Person:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="contact_person"
                                                    value="{{ old('contact_person', isset($data) ? $data->contact_name : '') }}">

                                                @if ($errors->has('contact_person'))
                                                    <span class="help-block">{{ $errors->first('contact_person') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Contact Number:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="mobile"
                                                    value="{{ old('mobile', isset($data) ? $data->mobile : '') }}"
                                                    required>

                                                @if ($errors->has('mobile'))
                                                    <span class="help-block">{{ $errors->first('mobile') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            <label
                                                class="control-label col-sm-3 {{ isset($create) ? '' : '' }}">Email:</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ old('email', isset($data) ? $data->email : '') }}">

                                                @if ($errors->has('email'))
                                                    <span class="help-block">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Address:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="address"
                                                    value="{{ old('address', isset($data) ? $data->address : '') }}">

                                                @if ($errors->has('address'))
                                                    <span class="help-block">{{ $errors->first('address') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('shipping_address') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Shipping Address:</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="shipping_address"
                                                    value="{{ old('shipping_address', isset($data) ? $data->shipping_address : '') }}">

                                                @if ($errors->has('shipping_address'))
                                                    <span class="help-block">{{ $errors->first('shipping_address') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('shipping_rate_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Shipping Method:</label>
                                            <div class="col-sm-9">
                                                <select name="shipping_rate_id" class="form-control select2">
                                                    <option value=""> Select Shipping Method</option>
                                                    @php($shipping_rate_id = old('shipping_rate_id', isset($data) ? $data->shipping_rate_id : ''))
                                                    @foreach ($shipping_methods as $shipping_method)
                                                        <option value="{{ $shipping_method->id }}"
                                                            {{ $shipping_rate_id == $shipping_method->id ? 'selected' : '' }}>{{ $shipping_method->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('shipping_rate_id'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('shipping_rate_id') }}</strong>
                                                    </span>
                                                @endif
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

                                        <div class="form-group">
                                            <div class="col-sm-offset-3 text-center">
                                                <button type="submit"
                                                    class="btn btn-success btn-flat">{{ isset($edit) ? __('Update') : __('Create') }}</button>
                                                <button type="reset"
                                                    class="btn btn-warning btn-flat">{{ __('Clear') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.user.customer.index') }}" class="form-inline">
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
                                        <button type="submit" class="btn btn-info btn-flat">{{ __('Search') }}</button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.user.customer.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Admin/Seller/Reseller</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Contact Person</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $key=>$val)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $val->user->name }}</td>
                                            <td>{{ $val->name }}</td>
                                            <td>{{ $val->mobile }}</td>
                                            <td>{{ $val->email }}</td>
                                            <td>{{ $val->contact_name }}</td>
                                            <td>{{ $val->address }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show customer')
                                                            <li><a
                                                                    href="{{ route('admin.user.customer.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @can('edit customer')
                                                            <li><a
                                                                    href="{{ route('admin.user.customer.edit', $val->id) . qString() }}"><i
                                                                        class="fa fa-pencil"></i> Edit</a></li>
                                                        @endcan

                                                        @can('delete customer')
                                                            <li><a
                                                                    onclick="activity('{{ route('admin.user.customer.status', $val->id) . qString() }}')"><i
                                                                        class="fa fa-toggle-off"></i>
                                                                    {{ $val->status == 'Active' ? 'Deactivated' : 'Active' }}</a>
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
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.user.customer.import') . qString() }}"
                enctype="multipart/form-data" class="non-validate">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Import Customer</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            Example Excel file download <a href="{{ asset('excels/customers.xlsx') }}">click here</a>
                        </div>

                        <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                            <label class="required">xlsx:</label>
                            <input type="file" class="form-control" name="file" required>

                            @if ($errors->has('file'))
                                <span class="help-block">{{ $errors->first('file') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $( document ).ready(function() {
            $('#type').on('change', function(){
                let role = $(this).val();
                if ( role == "Admin") {
                    $('#user').hide();
                } else {
                        $('#user').show();
                        $.ajax({
                        url: '{{ route('admin.user.getuser') }}',
                        type: "GET",
                        dataType: 'json',
                        data: {
                            role: role,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var html = '';
                            $( response.data ).each(function( index , val ) {
                                html += ` <option value="${val.id}"> ${val.name}</option> `;
                            });
                            $('#user_id').html(html);
                        }
                    })
                }
            });
        });
        
    </script>
@endpush