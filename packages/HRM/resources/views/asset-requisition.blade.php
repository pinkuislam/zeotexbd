@extends(config('hrm.layout_view'))

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('oshnisoft-hrm.asset-requisition.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Asset Requisition List
                    </a>
                </li>

                @if (isset($show))
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Asset Requisition Details
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
                                    <th style="width:120px;">Employee</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ $data->employee != null ? $data->employee->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ $data->created_at }}</td>
                                </tr>
                                <tr>
                                    <th>Expected Date</th>
                                    <th>:</th>
                                    <td>{{ $data->expected_date }}</td>
                                </tr>
                                <tr>
                                    <th>Item</th>
                                    <th>:</th>
                                    <td>{{ $data->item }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{{ $data->note }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <th>:</th>
                                    <td>{{ $data->quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th>:</th>
                                    <td>{{ $data->status }}</td>
                                </tr>
                                <tr>
                                    <th>Feedback</th>
                                    <th>:</th>
                                    <td>{{ $data->feedback }}</td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <th>:</th>
                                    <td>{{ $data->updater != null ? $data->updater->name : '-' }}</td>
                                </tr>

                                @if ($data->status == 'Pending')
                                <tr>
                                    <td colspan="3">
                                        <form method="POST" action="{{ route('oshnisoft-hrm.asset-requisition.update', $data->id) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group">
                                                <label>Feedback:</label>
                                                <textarea class="form-control" name="feedback" rows="6"></textarea>
                                            </div>

                                            <div class="text-right">
                                                <button type="submit" name="status" value="Approved" class="btn btn-success btn-flat">Approved</button>
                                                <button type="submit" name="status" value="Canceled" class="btn btn-danger btn-flat">Canceled</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                @endif


                            </table>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('oshnisoft-hrm.asset-requisition.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                    </div>

                                    <div class="form-group">
                                        <select name="status" class="form-control">
                                            <option value="">Any Status</option>
                                            @foreach (['Pending', 'Canceled', 'Approved'] as $sts)
                                                <option value="{{ $sts }}" {{ Request::get('status') == $sts ? 'selected' : '' }}>{{ $sts }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn-info btn-flat">Search</button>
                                        <a class="btn btn-warning btn-flat" href="{{ route('oshnisoft-hrm.asset-requisition.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Expected Date</th>
                                        <th>Employee</th>
                                        <th>Item</th>
                                        <th>Note</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $val)
                                        <tr>
                                            <td>{{ $val->expected_date }}</td>
                                            <td>{{ $val->employee != null ? $val->employee->name : '-' }}</td>
                                            <td>{{ $val->item }}</td>
                                            <td>{{ $val->note }}</td>
                                            <td>{{ $val->quantity }}</td>
                                            <td>{{ $val->status }}</td>
                                            <td>
                                                <a href="{{ route('oshnisoft-hrm.asset-requisition.show', $val->id) . qString() }}" class="btn btn-default btn-flat btn-xs"><i class="fa fa-eye"></i> Show</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <x-sp-components::pagination-row :records="$records" />
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
