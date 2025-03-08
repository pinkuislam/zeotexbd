@extends('layouts.app')
@push('styles')
<style>
    .show_image{
        height: 80px !important;
        width: 80px !important;
    }
</style>    
@endpush
@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.accessory.purchase_returns.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Accessory Purchase Return List
                    </a>
                </li>

                @can('add accessory-purchase_return')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.accessory.purchase_returns.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Accessory Purchase Return
                        </a>
                    </li>
                @endcan

                @can('edit accessory-purchase_return')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Accessory Purchase Return
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show accessory-purchase_return')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Accessory Purchase Return Detail
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
                                    <th>Supplier</th>
                                    <th>:</th>
                                    <td>{{ optional($data->supplier)->name }}</td>
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
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>accessory</th>
                                        <th style="text-align: right;">Quantity</th>
                                        <th style="text-align: right;">Unit Price</th>
                                        <th style="text-align: right;">Total Price</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($data->items as $key => $val)
                                        <tr>
                                            <td>{{ optional($val->accessory)->name }}</td>
                                            <td style="text-align: right;">{{ number_format($val->quantity, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->unit_price, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;">Total Quantity :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->items->sum('quantity'), 2) }}
                                        </th>
                                        <th style="text-align: right;">Sub Total Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->subtotal_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="3">Cost:</th>
                                        <th style="text-align: right;">{{ number_format($data->cost, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="3">Total Amount :</th>
                                        <th style="text-align: right;">{{ number_format($data->total_amount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.accessory.purchase_returns.update', $edit) : route('admin.accessory.purchase_returns.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}"
                                                    required>

                                                @if ($errors->has('date'))
                                                    <span class="help-block">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Supplier :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="supplier_id" required id="supplier_id">
                                                    <option value="">Select Supplier</option>
                                                    @php($supplier_id = old('supplier_id', isset($data) ? $data->supplier_id : ''))
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ $supplier_id == $supplier->id ? 'selected' : '' }}>
                                                            {{ $supplier->name }} </option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('supplier_id'))
                                                    <span class="help-block">{{ $errors->first('supplier_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Purchase :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="purchase_id" required id="purchase_id">
                                                    <option value="">Select Purchase</option>
                                                    @php($purchase_id = old('purchase_id', isset($data) ? $data->purchase_id : ''))
                                                    @if (isset($data)) 
                                                    @foreach ($purchases as $purchase)
                                                        <option value="{{ $purchase->id }}"
                                                            {{ $purchase_id == $purchase->id ? 'selected' : '' }}>
                                                            {{ $purchase->date }} - {{ $purchase->code }} </option>
                                                    @endforeach
                                                    @endif
                                                </select>

                                                @if ($errors->has('purchase_id'))
                                                    <span class="help-block">{{ $errors->first('purchase_id') }}</span>
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
                                    </div>
                                </div>

                                <div class="box-body table-responsive" id="purchase"  style="display: @if (isset($data)) block @else none @endif">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="required">accessory</th>
                                                <th>Purchase Quantity</th>
                                                <th>Used Quantity</th>
                                                <th>Remain Quantity</th>
                                                <th>Quantity</th>
                                                <th class="required">Unit Price</th>
                                                <th class="required">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="responseHtml">
                                            <?php
                                                $sutTotal = 0;
                                            ?>
                                            @foreach ($items as $key => $item)
                                                <tr class="subRow">
                                                    <td>
                                                        <input type="text" id="accessory_id{{ $key }}" class="form-control" value="{{ $item->accessory ? $item->accessory->name : ''  }}" readonly>
                                                        <input type="hidden" name="accessory_id[]" class="form-control" value="{{ $item->accessory ? $item->accessory->id : '' }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" id="purchase_quantity{{ $key }}" value="{{ $item->quantity }}" readonly>
                                                    </td>
                                                    <td>
                                                       <?php 
                                                       if (isset($data)) {
                                                        $qty = $data->items[$key]->quantity;
                                                       } else {
                                                        $qty = 0;
                                                       }
                                                       $sutTotal += $item->unit_price * $qty;
                                                       ?>
                                                        <input type="number" class="form-control" id="used_quantity{{ $key }}" value="{{ $item->used_quantity - $qty }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" id="remain_quantity{{ $key }}" value="{{  $remain_qty = $item->quantity - $item->used_quantity + $qty }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control qty" name="quantity[]" id="quantity{{ $key }}" onclick="checkItemQuantity({{ $key }})" onkeyup="checkItemQuantity({{ $key }})" value="{{ $qty }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="unit_price[]" id="unit_price{{ $key }}" value="{{ $item->unit_price }}" onkeyup="chkItemPrice({{ $key }})" onclick="chkItemPrice({{ $key }})">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="amount[]" id="amount{{ $key }}" value="{{ $item->unit_price * $qty  }}" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td class="text-right" colspan="4"><strong>Total Quantity
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_quantity" id="total_quantity"
                                                        value="{{ isset($data) ? $data->total_quantity : '' }}">
                                                </td>
                                                <td class="text-right"><strong>Sub Total Amount
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="subtotal_amount" id="subtotal_amount"
                                                        value="{{ isset($data) ? numberFormat($sutTotal) : '' }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6">
                                                    <strong>Cost :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" onkeyup="totalCal()" class="form-control"
                                                        name="cost" id="cost"
                                                        value="{{ old('cost', isset($data) ? $data->cost : '') }}">
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-right" colspan="6"><strong>Total Amount :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_amount" id="total_amount"
                                                        value="{{ old('total_amount', isset($data) ? $data->total_amount : '') }}">
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
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
                        <form method="GET" action="{{ route('admin.accessory.purchase_returns.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">

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
                                            href="{{ route('admin.accessory.purchase_returns.index') }}">X</a>
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
                                        <th>Total Quantity</th>
                                        <th>Total Amount</th>
                                        <th>Created By</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>
                                                {{ $val->total_quantity }}
                                            </td>
                                            <td>
                                                {{ number_format($val->total_amount, 2) }}
                                            </td>
                                            <td>{{ optional($val->createdBy)->name }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show accessory-purchase_return')
                                                            <li><a href="{{ route('admin.accessory.purchase_returns.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan
                                                        @if ($val->items->sum('used_quantity') == 0)
                                                            @can('edit accessory-purchase_return')
                                                                <li><a href="{{ route('admin.accessory.purchase_returns.edit', $val->id) . qString() }}"><i
                                                                            class="fa fa-pencil"></i> Edit</a></li>
                                                            @endcan
                                                            @can('delete accessory-purchase_return')
                                                                <li><a onclick="deleted('{{ route('admin.accessory.purchase_returns.destroy', $val->id) . qString() }}')"><i
                                                                            class="fa fa-close"></i> Delete</a></li>
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
                        <div class="row">
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($result) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $result->appends(Request::except('page'))->links() }}
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
   $(document).on('change', '#supplier_id', function(){
        var id = $(this).val();
        $.ajax({
            url: '{{ route("admin.accessory.supplier.purchase") }}',
            type: "GET",
            dataType: 'json',
            data: {
                id: id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                var html = '';
                html += `<option value=""> Secect Purchase</option>`;
                $( response.data ).each(function( index , val ) {
                    html += ` <option value="${val.id}"> ${val.date} - ${val.code}</option> `;
                });
                $('#purchase_id').html(html);
            }
        });
    });
   $(document).on('change', '#purchase_id', function(){
        var id = $(this).val();
        $.ajax({
            url: '{{ route("admin.accessory.getpurchase") }}',
            type: "GET",
            dataType: 'json',
            data: {
                id: id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.data) {
                    $('#purchase').show();
                    var html = '';
                    var total_quantity = 0;
                    var total_amount = 0;
                    $( response.data.items ).each(function( index , val ) {
                        let accessory = val.accessory ? val.accessory.name : '';
                        let remainQty = Number(val.quantity) - Number(val.used_quantity);
                        total_quantity += remainQty;
                        total_amount += Number(val.unit_price * remainQty);
                        html += ` 
                        <tr class="subRow">
                            <td>
                                <input type="text" id="accessory_id${index}" class="form-control" value="${accessory}" readonly>
                                <input type="hidden" name="accessory_id[]" class="form-control" value="${val.accessory_id}">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="purchase_quantity${index}" value="${val.quantity}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="used_quantity${index}" value="${val.used_quantity}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="remain_quantity${index}" value="${remainQty}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control qty" name="quantity[]" id="quantity${index}" onclick="checkItemQuantity(${index})" onkeyup="checkItemQuantity(${index})" value="${remainQty}">
                            </td>
                            <td>
                                <input type="number" class="form-control" name="unit_price[]" id="unit_price${index}" value="${val.unit_price}" onkeyup="chkItemPrice(${index})" onclick="chkItemPrice(${index})">
                            </td>
                            <td>
                                <input type="number" class="form-control total_price" name="amount[]" id="amount${index}" value="${val.unit_price * remainQty}" readonly>
                            </td>
                        </tr>
                        `;
                    });
                    $('#responseHtml').html(html);
                    $('#total_quantity').val(total_quantity);
                    $('#subtotal_amount').val(total_amount);
                    $('#total_amount').val(total_amount);
                }
            }
        });
    });


        function checkItemQuantity(key) {
            var quantity = Number($('#quantity' + key).val());
            var remain_quantity = Number($('#remain_quantity' + key).val());

            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Please Provide Valid Quantity!');
            }
            if (quantity > remain_quantity) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Quantity greater then remain quantity!');
            }
            chkItemPrice(key);
        }

        function chkItemPrice(key) {
            var quantity = Number($('#quantity' + key).val());
            var unit_price = Number($('#unit_price' + key).val());

            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Please Provide Valid Quantity!');
            }
            if (isNaN(unit_price)) {
                $('#unit_price' + key).val('');
                $('#unit_price' + key).focus();
                alerts('Please Provide Valid Price!');
            }
            if (quantity > 0 && unit_price > 0)
            var total = Number(quantity * unit_price);
            $('#amount' + key).val(total);
            totalCal();
        }

        function totalCal() {
            var quantity = 0;
            $("input[id^='quantity']").each(function() {
                quantity += +$(this).val();
            });
            $('#total_quantity').val(quantity);

            var subTotal = 0;
            $("input[id^='amount']").each(function() {
                subTotal += Number($(this).val());
            });
            $('#subtotal_amount').val(Number(subTotal).toFixed(2));
            var cost = Number($('#cost').val());
            var total = ((subTotal  + cost));
            $('#total_amount').val(Number(total).toFixed(2));
        }
        
    </script>
@endpush
