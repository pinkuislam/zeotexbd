@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.bonus-setup.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Bonus Setup List
                    </a>
                </li>
                @can('add hr_bonus-setup')
                <li {{ isset($create) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.bonus-setup.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Bonus Setup
                    </a>
                </li>
                @endcan

                @if (isset($edit))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-edit" aria-hidden="true"></i> Edit Bonus Setup
                        </a>
                    </li>
                @endif

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Bonus Setup Details
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
                                    <th style="width:120px;">Bonus Title</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->title }}</td>
                                </tr>
                                <tr>
                                    <th>Percentage</th>
                                    <th>:</th>
                                    <td>{{ $data->percent . '% Based on '. $data->percent_type . ' Salary' }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ $data->bonus_date }}</td>
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
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('oshnisoft-hrm.bonus-setup.update', $edit) : route('oshnisoft-hrm.bonus-setup.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Bonus Title :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="title"
                                                    value="{{ old('title', isset($data) ? $data->title : '') }}"
                                                    required>

                                                @if ($errors->has('title'))
                                                    <span
                                                        class="help-block">{{ $errors->first('title') }}</span>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="form-group{{ $errors->has('bonus_date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Bonus Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="bonus_date"
                                                    value="{{ old('bonus_date', isset($data) ? $data->bonus_date : date('Y-m-d')) }}"
                                                    required>

                                                @if ($errors->has('bonus_date'))
                                                    <span
                                                        class="help-block">{{ $errors->first('bonus_date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('percent_type') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Bonus Based on:</label>
                                            <div class="col-sm-9">
                                                <select name="percent_type" class="form-control select2" required>
                                                    @php($percent_type = old('percent_type', isset($data) ? $data->percent_type : ''))
                                                    @foreach (['Basic', 'Gross'] as $sts)
                                                        <option value="{{ $sts }}"
                                                            {{ $percent_type == $sts ? 'selected' : '' }}>{{ $sts }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('percent_type'))
                                                    <span class="help-block">{{ $errors->first('percent_type') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('percent') ? ' has-error' : '' }}">
                                            <label class="control-label required col-sm-3">Percentange:</label>
                                            <div class="col-sm-9">
                                                <input type="number" name="percent" class="form-control" value="{{ old('percent', isset($data) ? $data->percent : '') }}" required>
                                                @if ($errors->has('percent'))
                                                    <span class="help-block">{{ $errors->first('percent') }}</span>
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
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('oshnisoft-hrm.bonus-setup.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">Any Status</option>
                                            @foreach (['Active', 'Processed', 'Canceled'] as $sts)
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
                                        <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.bonus-setup.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Bonus Title</th>
                                        <th>Bonus Date</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bonuses as $val)
                                        <tr>
                                            <td>{{ $val->title }}</td>
                                            <td>{{ $val->bonus_date }}</td>
                                            <td>{{ $val->percent . '% Based on '. $val->percent_type . ' Salary' }}</td>
                                            <td>
                                                {{ $val->status }}
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button"
                                                        data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show hr_bonus-setup')
                                                        <li><a href="{{ route('oshnisoft-hrm.bonus-setup.show', $val->id) . qString() }}"><i class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @if($val->status == 'Active')
                                                            @can('edit hr_bonus-setup')
                                                                <li><a href="{{ route('oshnisoft-hrm.bonus-setup.edit', $val->id) . qString() }}"><i class="fa fa-eye"></i> Edit</a></li>
                                                            @endcan

                                                            @can('delete hr_bonus-setup')
                                                                <li><a onclick="deleted('{{ route('oshnisoft-hrm.bonus-setup.destroy', $val->id) . qString() }}')"><i class="fa fa-close"></i> Delete</a></li>
                                                            @endcan
                                                            @can('cancel hr_bonus-setup')
                                                                <li><a onclick="activity('{{ route('oshnisoft-hrm.bonus-setup.reject', $val->id) . qString() }}', 'Are you sure to reject this bonus?')"><i class="fa fa-close"></i> Cancel</a></li>
                                                            @endcan
                                                        @endif


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
