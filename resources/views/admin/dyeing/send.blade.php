@extends('layouts.app')   
@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.send-dyeing.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Send List
                    </a>
                </li>

                @can('add send-dyeing')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.send-dyeing.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Send
                        </a>
                    </li>
                @endcan

                @can('edit send-dyeing')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Send
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show send-dyeing')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Send Detail
                            </a>
                        </li>
                    @endif
                @endcan
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Code</th>
                                    <th>:</th>
                                    <td>{{ $data->code }}</td>
                                </tr>
                                <tr>
                                    <th>Dyeing Agent</th>
                                    <th>:</th>
                                    <td>
                                        @if($data->dyeingAgent)
                                            {{ $data->dyeingAgent->name }} - {{ $data->dyeingAgent->mobile }} 
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->date) }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{{ $data->note ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <th>:</th>
                                    <td>{{ $data->greyItems[0]->quantity . ' ' .  $data->greyItems[0]->product->unit->name}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.send-dyeing.update', $edit) : route('admin.send-dyeing.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="form-group">
                                        <label class="control-label col-sm-3 required">Dyeing Agent :</label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="dyeing_agent_id" required id="dyeing_agent_id">
                                                <option value="">Select Dyeing Agent</option>
                                                @php($dyeing_agent_id = old('dyeing_agent_id', isset($data) ? $data->dyeing_agent_id : ''))
                                                @foreach ($dyeingAgents as $dyeing_agent)
                                                    <option value="{{ $dyeing_agent->id }}"
                                                        {{ $dyeing_agent_id == $dyeing_agent->id ? 'selected' : '' }}>
                                                        {{ $dyeing_agent->name }} </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('dyeing_agent_id'))
                                                <span class="text-danger">{{ $errors->first('dyeing_agent_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 required">Date :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control datepicker" name="date"
                                                value="{{ old('date', isset($data) ? dbDateFormat($data->date) : date('d-m-Y')) }}"
                                                required>

                                            @if ($errors->has('date'))
                                                <span class="help-block">{{ $errors->first('date') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                        
                                    <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3">Note :</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="note"
                                                value="{{ old('note', isset($data) ? $data->note : '') }}">

                                            @if ($errors->has('note'))
                                                <span class="help-block">{{ $errors->first('note') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('stock') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3">Stock Qty (In {{ $product->unit->name}}):</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="stock" id="stock"
                                                value="{{ old('stock', isset($stock) ? $stock : '') }}" readonly>

                                            @if ($errors->has('stock'))
                                                <span class="help-block">{{ $errors->first('stock') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 required">Sending Qty (In {{ $product->unit->name}}):</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="quantity" id="quantity"
                                                value="{{ old('quantity', isset($data) ? $data->greyItems->quantity : '') }}" required>
                                            <input type="hidden" class="form-control" name="product_id"
                                                value="{{ old('product_id', isset($data) ? $data->greyItems->product_id : $product->id) }}">
                                            <input type="hidden" class="form-control" name="unit_id"
                                                value="{{ old('unit_id', isset($data) ? $data->greyItems->product->unit->id : $product->unit->id) }}">

                                            @if ($errors->has('quantity'))
                                                <span class="help-block">{{ $errors->first('quantity') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="text-center">
                                        <button type="submit"
                                            class="btn btn-success btn-flat">{{ isset($edit) ? __('Update') : __('Submit') }}</button>
                                        <button type="reset"
                                            class="btn btn-warning btn-flat">{{ __('Clear') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.send-dyeing.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select class="form-control select2" name="dyeing_agent_id">
                                            <option value="">Any Agent</option>
                                            @foreach ($dyeingAgents as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ Request::get('dyeing_agent_id') == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name . '-'. $val->mobile }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="from" id="datepickerFrom"
                                            value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="to" id="datepickerTo"
                                            value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="q" value="" placeholder="Write your search text...">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn-info btn-flat">{{ __('Search') }}</button>
                                        <a class="btn btn-warning btn-flat"
                                            href="{{ route('admin.send-dyeing.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Date</th>
                                        <th>Dyeing Agent</th>
                                        <th>Quanty</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ optional($val->dyeingAgent)->name }}</td>
                                            <td>
                                                {{ $val->greyItems[0]->quantity . ' ' .  $val->greyItems[0]->product->unit->name }}
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show send-dyeing')
                                                            <li><a
                                                                    href="{{ route('admin.send-dyeing.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        @can('delete send-dyeing')
                                                            <li><a
                                                                    onclick="deleted('{{ route('admin.send-dyeing.destroy', $val->id) . qString() }}')"><i
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
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($data) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $data->appends(Request::except('page'))->links() }}
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

@push('scripts')
    <script>
        $( document ).ready(function() {
            $('#quantity').on('change', function(){
                var quantity = Number($('#quantity').val());
                var stock = Number($('#stock').val());
                if(isNaN(quantity) || isNaN(stock)){
                    $('#quantity').val('');
                    alerts('Please Provide Valid Quantity!');
                } else {
                    if(quantity > stock){
                        $('#quantity').val('');
                        alerts('Can not greater than stock quantity!!');
                    }
                }

            });
        });

    </script>
@endpush