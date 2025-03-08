<div class="row">
    <div class="col-sm-12">
        <input type="hidden" class="form-control" name="type" id="type" value="{{ $type }}" required>
        <div class="col-sm-12">
            <div class="form-group">
                <label class="control-label col-sm-3">Search Order:</label>
                <div class="col-sm-8" style="padding: 0px !important">
                    <input type="text" id="orderSearch" class="form-control" id="order_id" placeholder="Search Order" value="{{ isset($order) ? $order->code : ''}}"">
                    <input type="hidden" name="order_id" id="order" value="{{isset($data) ? $data->order_id : ''}}">
                </div>
                <div class="col-sm-1">
                    <a onclick="geOrder()" class="btn btn-info">Search Order</a>
                </div>
            </div>
        </div>
        @if (isset($data))  
        <div class="form-group{{ $errors->has('receipt_no') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3">Receipt No.:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="receipt_no"
                    value="{{ old('receipt_no', isset($data) ? $data->receipt_no : '') }}"
                    readonly>

                @if ($errors->has('receipt_no'))
                    <span class="help-block">{{ $errors->first('receipt_no') }}</span>
                @endif
            </div>
        </div>
        @endif

        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 required">Date :</label>
            <div class="col-sm-9">
                <input type="text" class="form-control datepicker" name="date"
                    value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : (Session::has('payDate') ? Session::get('payDate') : date('Y-m-d'))) }}"
                    required>

                @if ($errors->has('date'))
                    <span class="help-block">{{ $errors->first('date') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group{{ $errors->has('customer_id') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 required">Customer:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control"
                value="{{ old('customer_id', isset($data) ? $data->customer->name . '-'. $data->customer->mobile. '-'. $data->customer->address  : '') }}"
                required readonly id="customer">
                <input type="hidden" name="customer_id" value="{{ old('customer_id', isset($data) ?$data->customer_id : '') }}" id="customer_id">

                @if ($errors->has('customer_id'))
                    <span class="help-block">{{ $errors->first('customer_id') }}</span>
                @endif
            </div>
        </div>
        @php
            if (isset($order) ) {
                $order_amount = $order->amount;
                $customerPayment = $order->customerPayment;
                $pay = $items->sum('amount');
                $customer_pay = ($customerPayment - $pay);
                $order_due = ($order_amount - $customer_pay);
            }
        @endphp
        <div class="form-group">
            <label class="control-label col-sm-3">Order Amount:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="order_amount" id="order_amount" readonly value="{{ isset($order) ? $order_amount : ''}}">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Pay Amount:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="pay_amount" readonly value="{{ isset($order) ? $customer_pay : ''}}">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Due Amount:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="due" readonly value="{{ isset($order) ?  $order_due : ''}}">
            </div>
        </div>

        <h3 id="orderInfo" style="display: none; text-align:center"></h3>
        <div class="box-body table-responsive bankdiv">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th class="required">Bank</th>
                        <th>Bank Balance</th>
                        <th class="required">Amount</th>
                        <th>Bank Final Balance</th>
                    </tr>
                </thead>

                <tbody id="responseHtml">

                    @foreach ($items as $key => $item)
                        <tr class="subRow" id="row{{ $key }}">
                            <input type="hidden" name="transaction_id[]"
                                value="{{ $item->id }}">
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
                                <select name="bank_id[]" id="bank_id{{ $key }}"
                                    class="form-control select2" required onchange="checkBank({{ $key }})">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}"
                                            {{ $item->bank_id == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->bank_name }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number" class="form-control" id="bank_balance{{ $key }}" readonly />
                            </td>
                            <td>
                                <input type="number" step="any" min="0"
                                    class="form-control amount" name="amount[]"
                                    id="amount{{ $key }}"
                                    value="{{ $item->amount }}"
                                    onchange="checkAmountCost({{ $key }})"
                                    onkeyup="checkAmountCost({{ $key }})"
                                    required>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="bank_final_balance{{ $key }}" readonly />
                            </td>
                        </tr>
                    @endforeach

                </tbody>

                <tfoot>
                    <tr>
                        <td class="text-right" colspan="3"><strong>Total
                                :</strong></td>

                        <td class="text-right"><input type="text" class="form-control"
                                readonly name="total_amount" id="total_amount"
                                value="{{ isset($data) ? $items->sum('amount') : '' }}">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        
        <div class="form-group">
            <label class="control-label col-sm-3">Final Order Due:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="final_balance" id="final_balance" readonly value="0">
            </div>
        </div>

        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3">Note :</label>
            <div class="col-sm-9">
                <textarea type="text" class="form-control" name="note" rows="4">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
                @if ($errors->has('note'))
                    <span class="help-block">{{ $errors->first('note') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group text-center">
            <button type="submit"
                class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
            <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var bankOptions = $('#bank_id' + key).html();

            var html = `<tr class="subRow" id="row` + newKey + `">
                <input type="hidden" name="transaction_id[]" value="0">
                <td><a class="btn btn-danger btn-flat" onclick="removeRow(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
                <td>
                    <select name="bank_id[]" id="bank_id` + newKey + `" class="form-control select2" required onchange="checkBank(` + newKey + `)">` + bankOptions + `</select>
                </td>
                <td>
                    <input type="number" class="form-control" id="bank_balance` + newKey + `" readonly />
                </td>
                <td>
                    <input type="number" step="any" min="0" name="amount[]" id="amount` + newKey +
                `" class="form-control amount" onchange="checkAmountCost(` + newKey + `)" onkeyup="checkAmountCost(` +
                newKey + `)" required/>
                </td>
                <td>
                    <input type="number" class="form-control" id="bank_final_balance` + newKey + `" readonly />
                </td>
            </tr>`;

            $('#responseHtml').append(html);
            $('#bank_id' + newKey).val('');
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select',
                tag: true
            });
        }

        function removeRow(key) {
            $('#row' + key).remove();
            remainingDue();
        }

        function geOrder() {
            let code = $('#orderSearch').val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: "{{ route('admin.payment.order') }}",
                data: {
                    code: code
                },
                success: (res) => {
                    if (res.success) {
                        $("#customer").val(`${res.data.customer.name} - ${res.data.customer.mobile} - ${res.data.customer.address}`);
                        $("#customer_id").val(res.data.customer.id);
                        $('#order_amount').val(res.data.total_amount);
                        $('#pay_amount').val(res.data.customer_pay);
                        $('#due').val(res.data.order_due);
                        $('#order').val(res.data.order_id);
                    } else {
                        alert(res.message);
                    }
                },
                error: (res) => {
                    alert(res.message);
                }
            });
        }

        function remainingDue() {
            let type = $('#type').val();
            let due = Number($('#due').val());
            let amount = Number($('#total_amount').val());

            let finalBalance = due;
            if (type == 'Payment') {
                finalBalance = (due + amount);
            } else {
                finalBalance = (due - amount);
            }

            $('#final_balance').val(Number(finalBalance));

            checkAddedRow();
        }

        function checkAddedRow() {
            let type = $('#type').val();
            
            var rowCount = $("tr[id^='row']").length;
            for (let index = 0; index < rowCount; index++) {
                let bankBalance = Number($(`#bank_balance${index}`).val());
                let amount = Number($(`#amount${index}`).val());

                let bankFinalBalance = 0;
                if (type == 'Payment') {
                    bankFinalBalance = (bankBalance - amount);
                } else {
                    bankFinalBalance = (bankBalance + amount);
                }

                if (bankFinalBalance >= 0) {
                    $(`#bank_final_balance${index}`).val(Number(bankFinalBalance));
                } else {
                    $(`#bank_final_balance${index}`).val(Number(bankBalance));
                    $(`#amount${index}`).val('');
                }
                checkAmountCost(index);
            }
        }

        function checkAmountCost(key) {
            
            let type = $('#type').val();
            let amount = Number($('#amount' + key).val());
            if (isNaN(amount)) {
                $('#amount' + key).val('');
                $('#amount' + key).focus();
                alerts('Please Provide Valid Amount!');
            }
            //bank portion

            let due = Number($('#bank_balance'+key).val());
            let finalbankBalance = 0;
            if (type == 'Payment') {
                finalbankBalance = (due - amount);
                if (finalbankBalance < 0) {
                    $('#amount'+key).val('');
                    finalbankBalance = due;
                    checkAmountCost(key);
                    alert('Final Bank balance will not be less than zero!');
                }
            } else {
                finalbankBalance = Number(due + amount);
            }
            $('#bank_final_balance'+key).val(Number(finalbankBalance));
            totalCal();
           let finalBalance = $('#final_balance').val();
           if (finalBalance < 0) {
                $('#amount'+key).val('');
                $('#final_balance').val(0);
                finalbankBalance = due;
                checkAmountCost(key);
                alert('Final Order Due will not be less than zero!');
            }
        }

        function totalCal() {
            let type = $('#type').val();
            var total_amount = 0;
            $(".amount").each(function() {
                total_amount += Number($(this).val());
            });
            $('#total_amount').val(Number(total_amount));
            let due = Number($('#due').val());
            let finalBalance = due;
            if (type == 'Payment') {
                finalBalance = Number(due + total_amount);
            } else {
                finalBalance = Number(due - total_amount);
            }
            $('#final_balance').val(Number(finalBalance));
        }

        function getBalanceForEdit() {
            var rowId = $(".subRow").length;
            for (var x = 0; x < rowId; x++) {
                checkBalanceForEdit(x);
            }
        }

        function checkBank(key) {
            var bankId = $('#bank_id' + key).val();
            var rowId = $(".subRow").length;
            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#bank_id' + x).val() == bankId) {
                        $('#bank_id' + key).val('');
                        alerts('This bank already exists in this payment!!');
                        return false;
                    }
                }
            }

            checkBalance(key);
        }

        function checkBalanceForEdit(key) {
            let id = $('#bank_id' + key).val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                url: "{{ route('admin.basic.bank.due') }}",
                beforeSend: () => {
                    $('#bank_balance'+key).val('');
                },
                success: (res) => {
                    if (res.success) {
                        $('#bank_balance'+key).val(res.due);
                        remainingBalanceForEdit(key);
                    } else {
                        alert(res.message);
                    }
                },
                error: (res) => {
                    alert(res.message);
                }
            });
        }

        function remainingBalanceForEdit(key) {
            let type = $('#type').val();
            let due = Number($('#bank_balance'+key).val());
            let amount = Number($('#amount'+key).val());
            $('#bank_final_balance'+key).val(due);
            if (type == 'Payment') {
                due = due + amount;
            } else {
                due = due - amount;
            }
            $('#bank_balance'+key).val(due);
            let orderDue = Number($('#due').val());
            let total_amount = Number($('#total_amount').val());
            let finalBalance = orderDue;
            if (type == 'Payment') {
                finalBalance = Number(orderDue + total_amount);
            } else {
                finalBalance = Number(orderDue - total_amount);
            }
            $('#final_balance').val(Number(finalBalance));
        }

        function checkBalance(key) {
            let id = $('#bank_id' + key).val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                url: "{{ route('admin.basic.bank.due') }}",
                beforeSend: () => {
                    $('#bank_balance'+key).val('');
                },
                success: (res) => {
                    if (res.success) {
                        $('#bank_balance'+key).val(res.due);
                        remainingBalance(key);
                    } else {
                        alert(res.message);
                    }
                },
                error: (res) => {
                    alert(res.message);
                }
            });
        }

        function remainingBalance(key) {
            let type = $('#type').val();
            let due = Number($('#bank_balance'+key).val());
            let amount = Number($('#total_amount'+key).val());
            let finalBalance = due;
            if (type == 'Payment') {
                finalBalance = (due - amount);
                if (finalBalance < 0) {
                    $('#amount'+key).val('');
                    finalBalance = due;
                    checkAmountCost(key);
                    alert('Final Bank balance will not be less than zero!');
                }
            } else {
                finalBalance = (due + amount);
            }
            $('#bank_final_balance'+key).val(finalBalance);
        }
        @if (isset($data))
            getBalanceForEdit();
        @endif
    </script>
@endpush