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
                    <a href="{{ route('admin.ecommerce.orders.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Processed Ecommarce Order To System Order
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ route('admin.ecommerce.orders.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="code" value="{{$data->serial_number}}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group {{ $errors->has('customer_name') ? ' has-error' : '' }} ">
                                            <label class="control-label col-sm-3 required">Customer Name :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="customer_name"
                                                    value="{{ $customer->name ?? $data->name }}" required>
                                                @if ($errors->has('customer_name'))
                                                    <span class="help-block">{{ $errors->first('customer_name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group {{ $errors->has('customer_mobile') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Customer Mobile No :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="customer_mobile"
                                                    value="{{ $customer->mobile ?? $data->phone }}" readonly >
                                                @if ($errors->has('customer_mobile'))
                                                    <span class="help-block">{{ $errors->first('customer_mobile') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group {{ $errors->has('customer_address') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Customer Address :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="customer_address" value="{{$customer->address ?? $data->address}}">
                                                @if ($errors->has('customer_address'))
                                                    <span class="help-block">{{ $errors->first('customer_address') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3 required">Delivery Agent :</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" name="delivery_agent_id" required id="delivery_agent_id">
                                                    <option value="">Select Delivery Agent</option>
                                                    @foreach ($delivery_agents as $delivery_agent)
                                                        <option value="{{ $delivery_agent->id }}">
                                                            {{ $delivery_agent->name }} </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('delivery_agent_id'))
                                                    <span class="text-danger">{{ $errors->first('delivery_agent_id') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{  date('d-m-Y') }}"
                                                    required>

                                                @if ($errors->has('date'))
                                                    <span class="help-block">{{ $errors->first('date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3">Collage :</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" id="image" name="image[]" multiple>
                                                @if (isset($data))
                                                    @if ($data->images)
                                                        @foreach ($data->images as $item)
                                                        {!! viewImg('orders', $item->image, ['popup' => 1, 'thumb' => 1, 'style' => 'width:50px;']) !!}
                                                        @endforeach
                                                    @endif
                                                @endif
                                                @if ($errors->has('image'))
                                                    <span class="help-block">{{ $errors->first('image') }}</span>
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
                                    <div class="col-md-6">
                                        <div>
                                            <h5> <strong>Customer Name:</strong> <span>{{$customer->name ?? ''}}</span></h5>
                                            <h5> <strong>Mobile Number:</strong> <span>{{$customer->mobile ?? ''}}</span></h5>
                                            <h5> <strong>Customer Address:</strong> <span>{{$customer->address ?? ''}}</span></h5>
                                            <h5> <strong>Customer Due:</strong> <span>{{$due ?? ''}}</span></h5>
                                            <h5> <strong>Customer Orders:</strong> <span>
                                                @if ($orders)
                                                    @foreach ($orders as $order)
                                                        <a href="{{ route('admin.orders.show', $order->id)}}">{{$order->code}}-{{$order->date}}</a> &nbsp;
                                                    @endforeach
                                                @endif
                                        </span></h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="required">Product</th>
                                                <th >Unit</th>
                                                <th >Color</th>
                                                <th class="required">Quantity</th>
                                                <th class="required">Unit Price</th>
                                                <th class="required">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="responseHtml">
                                            @foreach ($data->ecommerceOrders as $key => $item)
                                                <tr class="subRow" id="row{{ $key }}">
                                                    <td>
                                                        @if ($key == 0)
                                                            <a class="btn btn-success btn-flat"
                                                                onclick="addRow({{ $key }})"><i
                                                                    class="fa fa-plus"></i></a>
                                                        @else
                                                            <a class="btn btn-danger btn-flat"
                                                                onclick="removeRow({{ $key }})"><i
                                                                    class="fa fa-minus"></i></a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="text"  id="product_id{{ $key }}" class="form-control" readonly value="{{  $item->product_id ?  $item->product->name : ''}}">
                                                        <input type="hidden" name="product_id[]"  id="product{{ $key }}" class="form-control" value="{{  $item->product_id ?  $item->product->id : ''}}">
                                                    </td>
                                                    <td>
                                                        <input type="text"  id="unit_id{{ $key }}" class="form-control" readonly value="{{  $item->product_id ?  $item->product->unit->name : ''}}">
                                                        <input type="hidden" name="unit_id[]"  id="unit{{ $key }}" class="form-control" value="{{  $item->product_id ?  $item->product->unit_id : ''}}">
                                                    </td>
                                                    <td>
                                                        <input type="text"  id="color_id{{ $key }}" class="form-control" readonly value="{{  $item->color_id ?  $item->color->name : ''}}">
                                                        <input type="hidden" name="color_id[]"  id="color{{ $key }}" class="form-control" value="{{  $item->color_id ?  $item->color->id : ''}}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control qty" name="quantity[]"
                                                            id="quantity{{ $key }}"
                                                            value="{{ $item->quantity }}"
                                                            onclick="chkItemPrice({{ $key }})"
                                                            onkeyup="chkItemPrice({{ $key }})" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="unit_price[]" id="unit_price{{ $key }}"
                                                            value="{{ $item->sale_price }}"
                                                            onkeyup="chkItemPrice({{ $key }})" onclick="chkItemPrice({{ $key }})">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control total_price"
                                                            name="amount[]" id="amount{{ $key }}"
                                                            value="{{ $item->amount }}" readonly>
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
                                                        value="{{ $data->total_quantity }}">
                                                </td>
                                                <td class="text-right"><strong> Sub Total Amount
                                                        :</strong>
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        readonly name="sub_total_amount" id="sub_total_amount"
                                                        value="{{ numberFormat($data->sub_total_amount) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong> Shipping Charge
                                                    :</strong>
                                            </td>
                                            <td class="text-right"><input type="text" class="form-control"
                                                    name="shipping_charge" id="shipping_charge" onclick="totalCal()" onkeyup="totalCal()"
                                                    value="{{ numberFormat(optional($data->shipping)->rate)  }}">
                                            </td>
                                            </tr>
                                                <tr>
                                                    <td class="text-right" colspan="5"><strong> Advance Amount
                                                        :</strong>
                                                </td>
                                                <td>
                                                    @php($bank_id = old('bank_id', isset($data) ? $data->bank_id : ''))
                                                    <select class="form-control" name="bank_id">
                                                        <option value=""> Select Bank </option>
                                                        @foreach ($banks as $bnk)
                                                            <option value="{{ $bnk->id }}">{{ $bnk->bank_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('bank_id'))
                                                    <span class="help-block">{{ $errors->first('bank_id') }}</span>
                                                @endif
                                                </td>
                                                <td class="text-right"><input type="text" class="form-control"
                                                        name="advance_amount" onclick="totalCal()" onkeyup="totalCal()" id="advance_amount" value="0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong> Discount Amount:</strong></td>
                                                <td class="text-right"><input type="text" class="form-control" onclick="totalCal()" onkeyup="totalCal()"
                                                        name="discount_amount" id="discount_amount" value="0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong> Total Amount:</strong></td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control"
                                                     readonly name="total_amount" id="total_amount"
                                                    value="{{  numberFormat($data->total_amount) }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong> Remainning Due:</strong></td>
                                                <td class="text-right">
                                                    <input type="text" class="form-control"
                                                     readonly id="remainning_due"
                                                    value="{{ numberFormat($data->total_amount)}}">
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
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        function checkItemQuantity(key) {
            var quantity = Number($('#quantity' + key).val());

            if (isNaN(quantity)) {
                $('#quantity' + key).val('');
                $('#quantity' + key).focus();

                alerts('Please Provide Valid Quantity!');
            }
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
            var shipping_charge = Number($('#shipping_charge').val());
            var discount_amount = Number($('#discount_amount').val());
            var advance_amount = Number($('#advance_amount').val());
            var total =  Number(subTotal + shipping_charge - discount_amount);
            var remainning_due =  Number(total - advance_amount);
            $('#total_amount').val(Number(total).toFixed(2));
            $('#remainning_due').val(Number(remainning_due).toFixed(2));
        }
    </script>
@endpush