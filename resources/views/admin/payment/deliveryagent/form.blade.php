<div class="row">
    <div class="col-sm-12">
        <input type="hidden" class="form-control" name="type" id="type" value="{{ $type }}" required>

        <div class="form-group{{ $errors->has('receipt_no') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 required">Receipt No.:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="receipt_no"
                    value="{{ old('receipt_no', isset($data) ? $data->receipt_no : '') }}"
                    readonly>

                @if ($errors->has('receipt_no'))
                    <span class="help-block">{{ $errors->first('receipt_no') }}</span>
                @endif
            </div>
        </div>

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

        <div class="form-group{{ $errors->has('delivery_agent_id') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 required">Delivery Agent:</label>
            <div class="col-sm-9">
                <select name="delivery_agent_id" id="delivery_agent_id" class="form-control select2" required onchange="getDue(0)">
                    <option value="">Select deliveryagents</option>
                    @php($delivery_agent_id = old('delivery_agent_id', isset($data) ? $data->delivery_agent_id : ''))
                    @foreach ($deliveryagents as $cat)
                        <option value="{{ $cat->id }}"
                            {{ $delivery_agent_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('delivery_agent_id'))
                    <span class="help-block">{{ $errors->first('delivery_agent_id') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Due:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="due" readonly>
            </div>
        </div>

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
                            <input type="hidden" name="transaction_id[]" value="{{ $item->id }}">
                            <td>
                                @if ($key == 0)
                                    <a class="btn btn-success btn-flat" onclick="addRow({{ $key }})"><i class="fa fa-plus"></i></a>
                                @else
                                    <a class="btn btn-danger btn-flat" onclick="removeRow({{ $key }})"><i class="fa fa-minus"></i></a>
                                @endif
                            </td>

                            <td>
                                <select name="bank_id[]" id="bank_id{{ $key }}" class="form-control select2" required onchange="checkBank({{ $key }})">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ $item->bank_id == $bank->id ? 'selected' : '' }}>{{ $bank->bank_name }}</option>
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
                                    onclick="checkAmountCost({{ $key }})"
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
                        <td class="text-right" colspan="3"><strong>Total:</strong></td>

                        <td class="text-right"><input type="text" class="form-control"
                                readonly name="total_amount" id="total_amount"
                                value="{{ isset($data) ? $items->sum('amount') : '' }}">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        
        <div class="form-group">
            <label class="control-label col-sm-3">Final Balance:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="final_balance" readonly>
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
                `" class="form-control amount" onclick="checkAmountCost(` + newKey + `)" onkeyup="checkAmountCost(` +
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
        
        function getDue(isEdit) {
            let type = $('#type').val();
            let id = $('#delivery_agent_id').val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                data: {id: id},
                url: "{{ route('admin.user.delivery-agents.due') }}",
                beforeSend: () => {
                    $('#due').val('');
                },
                success: (res) => {
                    if (res.success) {
                        let due = Number(res.due);
                        if(isEdit){
                            let amount = Number($('#total_amount').val());
                            let finalBalance = due;
                            if (type == 'Payment') {
                                due = (due + amount);
                                finalBalance = (due - amount);
                            } else {
                                due = (due - amount);
                                finalBalance = (due + amount);
                            }
                            $('#due').val(due);
                            $('#final_balance').val(finalBalance);
                        } else {
                            $('#due').val(due);
                            remainingDue();
                        }
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
                finalBalance = Number(due - amount);
            } else {
                finalBalance = Number(due + amount);
            }

            $('#final_balance').val(finalBalance);
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
                    bankFinalBalance = Number((bankBalance - amount));
                } else {
                    bankFinalBalance = Number(bankBalance + amount);
                }
                if (bankFinalBalance >= 0) {
                    $(`#bank_final_balance${index}`).val(bankFinalBalance);
                } else {
                    $(`#bank_final_balance${index}`).val('');
                    $(`#amount${index}`).val('');
                }
                checkAmountCost(index);
            }
        }

        

        function checkAmountCost(key) {
            let type = $('#type').val();
            var total_amount = 0;
            let amount = Number($('#amount'+ key).val());
            //bank portion
            let due = Number($('#bank_balance'+key).val());
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
                finalBalance = Number(due + amount, 2);
            }
            $('#bank_final_balance'+key).val(finalBalance);

            totalCal();
        }

        function totalCal() {  
            let type = $('#type').val();
            var total_amount = 0;
            $(".amount").each(function() {
                total_amount += +$(this).val();
            });

            $('#total_amount').val(total_amount);

            let due = Number($('#due').val());
            let amount = Number($('#total_amount').val());
            let finalBalance = due;
            if (type == 'Payment') {
                finalBalance = (due - amount);
            } else {
                finalBalance = (due + amount);
            }

            $('#final_balance').val(finalBalance);
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
            let amount = Number($('#amount'+key).val());
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
            getDue(1);
            getBalanceForEdit();
        @endif
    </script>
@endpush