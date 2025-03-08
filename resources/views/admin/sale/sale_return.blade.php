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
                    <a href="{{ route('admin.sale.return.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Sale Return List
                    </a>
                </li>

                @can('add sale-return')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.sale.return.create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Sale Return
                        </a>
                    </li>
                @endcan

                @can('edit sale-return')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Sale Return
                            </a>
                        </li>
                    @endif
                @endcan

                @can('show sale-return')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Sale Return Detail
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
                                @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                    <tr>
                                        <th style="width:120px;">Type</th>
                                        <th style="width:10px;">:</th>
                                        <td>{{ $data->sale->type }}</td>
                                    </tr>
                                    @if ($data->sale->type == 'Seller' || $data->sale->type == 'Reseller')    
                                    <tr>
                                        <th style="width:120px;">{{ $data->sale->type }}</th>
                                        <th style="width:10px;">:</th>
                                        <td>{{ optional($data->user)->name }}</td>
                                    </tr>
                                    @endif
                                @endif
                                <tr>
                                    <th>Customer/Reseller Business </th>
                                    <th>:</th>
                                    <td>{{ $data->customer != null ? $data->customer->name : $data->resellerBusiness->name  }}</td>
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
                                        <th>SL</th>
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>Color</th>
                                        <th style="text-align: right;">Quantity</th>
                                        <th style="text-align: right;">Unit Price</th>
                                        <th style="text-align: right;">Total Price</th>
                                    </tr>
                                </thead>
                                <?php $subTotal = 0; ?>
                                <tbody>
                                    @foreach ($data->items as $key => $val)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $val->product ? $val->product->name : '-' }}</td>
                                            <td>{{ $val->unit ? $val->unit->name : '-' }}</td>
                                            <td>{{ $val->color ? $val->color->name : '-' }}</td>
                                            <td style="text-align: right;">{{ number_format($val->quantity, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->unit_price, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($val->unit_price * $val->quantity, 2) }}</td>
                                        </tr>
                                        <?php $subTotal += ( $val->unit_price * $val->quantity); ?>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;" colspan="4">Total Quantity :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->items->sum('quantity'), 2) }}
                                        </th>
                                        <th style="text-align: right;">SubTotal Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($subTotal, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="6">Deduction Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->deduction_amount, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="6">Cost :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->cost, 2) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right;" colspan="6">Total Amount :</th>
                                        <th style="text-align: right;">
                                            {{ number_format($data->return_amount, 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            @if ($code)
                            <div class="row">
                                <div class="col-md-8">
                                    <form method="get" action="{{ route('admin.sale.getsale') }}{{ qString() }}" class="form-horizontal">
                                    @csrf
                                        <div class="form-group ">
                                            <label for=" " class="control-label col-sm-3">Sale Code</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" name="code" id="search_sale" placeholder="Sale Code" required value="{{ $code }}">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="submit" class="btn btn-info btn-flat"> Search Sale</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.sale.return.update', $edit) : route('admin.sale.return.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                @if (isset($edit))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group ">
                                            <label for=" " class="control-label col-sm-3">Status</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"  readonly value="{{ $msg }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($sale)
                                
                                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                <div class="row">
                                    <div class="col-md-8">
                                        @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                            <div class="form-group">
                                                <label class="control-label col-sm-3 required">Type:</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="type" value="{{ $sale->type }}" readonly>
                                                    @if ($errors->has('type'))
                                                    <span class="help-block">{{ $errors->first('type') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($sale->type == 'Seller' || $sale->type == 'Reseller')
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3 required">Seller or Reseller:</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" value="{{ optional($sale->user)->name }}" readonly>
                                                        <input type="hidden" name="user_id" value="{{ $sale->user_id }}" >
                                                        <input type="hidden" name="reseller_amount" value="{{ $sale->reseller_amount }}" >
                                                        @if ($errors->has('user_id'))
                                                            <span class="help-block">{{ $errors->first('user_id') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        @if ($sale->customer)
                                            
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Customer :</label>
                                            <div class="col-sm-9">
                                                    <input type="text" class="form-control" value="{{ $sale->customer->name }}" readonly>
                                                    <input type="hidden" name="customer_id" value="{{ $sale->customer_id }}" >
                                                @if ($errors->has('customer_id'))
                                                    <span class="help-block">{{ $errors->first('customer_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                        @if ($sale->resellerBusiness) 
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Reseller Business :</label>
                                            <div class="col-sm-9">
                                                    <input type="text" class="form-control" value="{{ $sale->resellerBusiness->name }}" readonly>
                                                    <input type="hidden" name="reseller_business_id" value="{{ $sale->reseller_business_id }}" >
                                                @if ($errors->has('reseller_business_id'))
                                                    <span class="help-block">{{ $errors->first('reseller_business_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                     
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

                                <div class="box-body table-responsive" id="sale_table">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th class="required">Product</th>
                                                <th >Unit</th>
                                                <th >Color</th>
                                                <th >Sale Quantity</th>
                                                <th class="required">Quantity</th>
                                                <th class="required">Unit Price</th>
                                                <th class="required">Total Price</th>
                                            </tr>
                                        </thead>
                                        <?php 
                                            $subTotal = 0;
                                            $i = 0;
                                        ?>
                                        <tbody id="responseHtml">
                                            @if (count($sale->items) > 0)
                                                @foreach ( $sale->items as $key => $item) 
                                                    @foreach($item->items as $k => $baseItem)
                                                    <tr class="subRow" id="row{{ $i }}">
                                                        <td>
                                                            {{ $i + 1 }}
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" value="{{ $baseItem->product->name }}" readonly>
                                                            <input type="hidden" name="product_id[]" value="{{ $baseItem->product_id }}" id="product_id{{ $i }}">
                                                            <input type="hidden" name="product_out_id[]" value="{{ $baseItem->id }}" id="product_out_id{{ $i }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" value="{{ $baseItem->unit ? $baseItem->unit->name : '' }}" readonly>
                                                            <input type="hidden" name="unit_id[]" value="{{ $baseItem->unit_id }}" id="unit_id{{ $i }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" value="{{ $baseItem->color ? $baseItem->color->name: '' }}" readonly>
                                                            <input type="hidden" name="color_id[]" value="{{ $baseItem->color_id }}" id="color_id{{ $i }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="sale_quantity[]" id="sale_quantity{{ $i }}" value="{{ $baseItem->quantity }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control qty" name="quantity[]" id="quantity{{ $i }}" onclick="checkStock({{ $i }})" onkeyup="checkStock({{ $i }})" value="" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" name="unit_price[]" id="unit_price{{ $i }}" value="{{ $baseItem->unit_price }}" onkeyup="chkItemPrice({{ $i }})" onclick="chkItemPrice({{ $i }})">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control total_price" name="amount[]" id="amount{{ $i }}" value="" readonly>
                                                        </td>
                                                    </tr>
                                                    <?php 
        
                                                        $i += 1;
                                                    ?>
                                                    @endforeach
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-right" colspan="5"><strong>Total Quantity
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_quantity" id="total_quantity"
                                                        value="{{ isset($edit) ? $data->items->sum('quantity') : 0}}">
                                                </td>
                                                <td class="text-right"><strong> Sub Total Amount
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="sub_total_amount" id="sub_total_amount"
                                                        value="{{ isset($edit) ? numberFormat($subTotal) : numberFormat($subTotal) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7"><strong> Deduction Amount
                                                    :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" onkeyup="totalCal()" class="form-control" name="deduction_amount" id="deduction_amount" value="{{ isset($edit) ? numberFormat($data->deduction_amount) : 0  }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7"><strong> Cost
                                                    :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" onkeyup="totalCal()" class="form-control" name="cost" id="cost" value="{{ isset($edit) ? numberFormat($data->cost) : 0  }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="7"><strong> Total Amount
                                                    :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="total_amount" id="total_amount"
                                                        value="{{ isset($edit) ? numberFormat($data->total_amount) : numberFormat($subTotal)  }}">
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
                                @endif
                            </form>
                            @else
                            <div class="row">
                                <div class="col-md-8">
                                    <form method="get" action="{{ route('admin.sale.getsale') }}{{ qString() }}" class="form-horizontal">
                                    @csrf
                                        <div class="form-group ">
                                            <label for=" " class="control-label col-sm-3"> Sale Code</label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" name="code" id="search_sale" placeholder="Sale Code" required value="{{ $code }}">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <button type="submit" class="btn btn-info btn-flat"> Search Sale</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                            
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.sale.return.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select class="form-control select2" name="customer_id">
                                            <option value="">Any Customer</option>
                                            @foreach ($customers as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ Request::get('customer_id') == $val->id ? 'selected' : '' }}>
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
                                            href="{{ route('admin.sale.return.index') }}">X</a>
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
                                        <th>Admin/Seller/Reseller</th>
                                        <th>Customer/ Reseller Business</th>
                                        <th>Address</th>
                                        <th>Items</th>
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
                                            <td>{{ optional($val->user)->name }}</td>
                                            <td>
                                                @if ($val->customer)
                                                    {{$val->customer->name}} -  {{$val->customer->mobile}}
                                                @else
                                                {{$val->resellerBusiness ? $val->resellerBusiness->name . '-'. $val->resellerBusiness->mobile : ''}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($val->customer)
                                                    {{ $val->customer->address }}
                                                @else
                                                    {{ $val->resellerBusiness ? $val->resellerBusiness->address : '' }}
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($val->items as $key => $item)
                                                    {{ $item->product->name ?? '-' }}
                                                    <span class="label label-default">{{ number_format($item->quantity, 0) }} {{ $item->unit ? $item->unit->name : ''  }} {{ $item->color ? $item->color->name : ''  }}</span>
                                                    @if (($key + 1) % 3 == 0)
                                                    <br>
                                                    @else
                                                    @if ($loop->last)
                                                    @else
                                                    ,
                                                    @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ number_format($val->return_amount, 2) }}
                                            </td>
                                            <td>{{ isset($val->createdBy) ? $val->createdBy->name : '' }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                        type="button" data-toggle="dropdown">Action <span
                                                            class="caret"></span></a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('show sale-return')
                                                            <li><a
                                                                    href="{{ route('admin.sale.return.show', $val->id) . qString() }}"><i
                                                                        class="fa fa-eye"></i> Show</a></li>
                                                        @endcan

                                                        {{-- @can('delete sale-return')
                                                            <li><a
                                                                    onclick="deleted('{{ route('admin.sale.return.destroy', $val->id) . qString() }}')"><i
                                                                        class="fa fa-close"></i> Delete</a></li>
                                                        @endcan --}}
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
         function checkStock(key) {
            var quantity = Number($('#quantity' + key).val());
            var return_quantity = Number($('#return_quantity' + key).val());
            var sale_quantity = Number($('#sale_quantity' + key).val());
            var stock = Number(sale_quantity - return_quantity - quantity);
            if (stock < 0) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();
                alerts('Sale quantity not exist!');
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
            if (quantity > 0 && unit_price > 0)
            var total = Number(quantity * unit_price);
            $('#amount' + key).val(Number(total).toFixed(2));
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
            $('#sub_total_amount').val(Number(subTotal).toFixed(2));
            var deduction_amount = Number($('#deduction_amount').val());
            var cost = Number($('#cost').val());
            var total = Number((subTotal + cost + deduction_amount));
            $('#total_amount').val(Number(total).toFixed(2));
        }
    </script>
@endpush