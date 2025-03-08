@extends('layouts.app')
@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.receive-dyeing.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Receive List
                    </a>
                </li>

                @can('add receive-dyeing')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.receive-dyeing.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Receive
                        </a>
                    </li>
                @endcan

                @can('edit receive-dyeing')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Receive
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show receive-dyeing')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Receive Detail
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
                                    <th>Date</th>
                                    <th>:</th>
                                    <td>{{ dateFormat($data->date) }}</td>
                                </tr>
                                <tr>
                                    <th>Dyeing Agent</th>
                                    <th>:</th>
                                    <td>{{ optional($data->dyeingAgent)->name }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{{ $data->note ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Consumed Quantity</th>
                                    <th>:</th>
                                    <td>{{ $data->grey_fabric_consume}}</td>
                                </tr>
                                <tr>
                                    <th>Received Product</th>
                                    <th>:</th>
                                    <td>{{ $data->items[0]->product->name}}</td>
                                </tr>
                                
                                <tr>
                                    <th>Received Quantity</th>
                                    <th>:</th>
                                    <td>{{ $data->items[0]->quantity . ' ' .  $data->items[0]->product->unit->name}}</td>
                                </tr>

                                <tr>
                                    <th>Unit Price</th>
                                    <th>:</th>
                                    <td>{{ $data->unit_price}}</td>
                                </tr>
                                <tr>
                                    <th>Total Cost</th>
                                    <th>:</th>
                                    <td>{{ $data->total_cost}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.receive-dyeing.update', $edit) : route('admin.receive-dyeing.store') }}{{ qString() }}"
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
                                        <label class="control-label col-sm-3">Grey Stock Qty (In {{ $grey->unit->name}}):</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="stock" id="stock" readonly>
                                            @if ($errors->has('stock'))
                                                <span class="help-block">{{ $errors->first('stock') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('grey_fabric_consume') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 required">Consume Grey Fabric Qty (In {{ $grey->unit->name}}):</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="grey_fabric_consume" id="grey_fabric_consume" required>
                                            @if ($errors->has('grey_fabric_consume'))
                                                <span class="help-block">{{ $errors->first('grey_fabric_consume') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3 required">Received Products :</label>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="product_id" required id="product_id">
                                                <option value="">Select Received Product</option>
                                                @php($product_id = old('product_id', isset($data) ? $data->product_id : ''))
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ $product_id == $product->id ? 'selected' : '' }} 
                                                        data-unit={{ $product->unit->id}} 
                                                        data-unit_name={{ $product->unit->name}}>
                                                        {{ $product->name }} 
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('product_id'))
                                                <span class="text-danger">{{ $errors->first('product_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" class="form-control" name="unit_id" id="unit_id"
                                                value="{{ old('unit_id', isset($data) ? $data->items->product->unit->id : '') }}">
                                    
                                    <div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 required" id="label_received_quantity">Receive Qty:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="quantity" id="quantity"
                                                value="{{ old('quantity', isset($data) ? $data->items->quantity : '') }}" required>
                                            @if ($errors->has('quantity'))
                                                <span class="help-block">{{ $errors->first('quantity') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('unit_price') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 required" id="label_unit_price">Unit Price (In {{ $grey->unit->name}}):</label>
                                        <div class="col-sm-9">
                                            <input type="decimol" class="form-control" name="unit_price" id="unit_price"
                                                value="{{ old('unit_price', isset($data) ? $data->unit_price : '') }}" required>
                                            @if ($errors->has('unit_price'))
                                                <span class="help-block">{{ $errors->first('unit_price') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('total_cost') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 required">Total Cost:</label>
                                        <div class="col-sm-9">
                                            <input type="decimol" class="form-control" name="total_cost" id="total_cost"
                                                value="{{ old('total_cost', isset($data) ? $data->total_cost : '') }}" required>
                                            @if ($errors->has('total_cost'))
                                                <span class="help-block">{{ $errors->first('total_cost') }}</span>
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
                        <form method="GET" action="{{ route('admin.receive-dyeing.index') }}" class="form-inline">
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
                                            href="{{ route('admin.receive-dyeing.index') }}">X</a>
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
                                        <th>Consumed Grey</th>
                                        <th>Received Product</th>
                                        <th>Received Quantity</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ optional($val->dyeingAgent)->name }}</td>
                                            <td>{{ $val->grey_fabric_consume }}</td>
                                            <td>{{ optional($val->items[0])->product->name }}</td>
                                            <td>
                                                {{ $val->items[0]->quantity . ' ' .  $val->items[0]->product->unit->name }}
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show receive-dyeing')
                                                            <li><a
                                                                    href="{{ route('admin.receive-dyeing.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                        @can('delete receive-dyeing')
                                                            <li><a
                                                                    onclick="deleted('{{ route('admin.receive-dyeing.destroy', $val->id) . qString() }}')"><i
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
        $('#grey_fabric_consume').on('change', function(){
            var quantity = Number($('#grey_fabric_consume').val());
            var stock = Number($('#stock').val());
            if(isNaN(quantity) || isNaN(stock)){
                $('#grey_fabric_consume').val('');
                alerts('Please Provide Valid Quantity!');
            } else {
                if(quantity > stock){
                    $('#grey_fabric_consume').val('');
                    alerts('Can not greater than stock quantity!!');
                }
            }

        });

        //
        $('#dyeing_agent_id').on('change', function(){
                let id = $(this).val();
                $.ajax({
                    url: '{{ route('admin.dyeing.stock') }}',
                    type: "GET",
                    dataType: 'json',
                    data: {
                        agent_id: id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $("#stock").val(response.stock);
                        }
                    }
                })
            });

            $('#product_id').on('change', function(){
                $('#unit_id').val($('#product_id').find(':selected').data('unit'));
                let label = 'Received Qty In( ';
                let label2 = 'Unit Price In( ';
                let ll = $('#product_id').find(':selected').data('unit_name');
                $('#label_received_quantity').html(label + ll + ' )');
                $('#label_unit_price').html(label2 + ll + ' )');
            });

            $('#cost_per_unit').on('change', function(){
                var quantity = Number($('#quantity').val());
                var cost_per_unit = Number($('#cost_per_unit').val());
                if(isNaN(quantity) || isNaN(cost_per_unit)){
                $('#quantity').val('');
                alerts('Please Provide Valid Quantity!');
                } else {
                    $('#total_cost').val(quantity * cost_per_unit);
                }
            });
    });

</script>
@endpush