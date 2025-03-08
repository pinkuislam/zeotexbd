<div class="row">
    <div class="col-sm-12">
        <div class="form-group @error('type') has-error @enderror">
            <label class="control-label col-sm-3 required">Type:</label>
            <div class="col-sm-9">
                <select name="type" id="type" class="form-control select2" required onchange="calAll()">
                    @php($type = old('type', isset($data) ? $data->type : ''))
                    @foreach (['Received', 'Payment'] as $sts)
                        <option value="{{ $sts }}" {{ $type == $sts ? 'selected' : '' }}>{{ $sts }}</option>
                    @endforeach
                </select>

                @error('type')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group @error('date') has-error @enderror">
            <label class="control-label col-sm-3 required">Date :</label>
            <div class="col-sm-9">
                <input type="text" class="form-control datepicker" name="date" value="{{ old('date', isset($data) ? $data->date :  date('Y-m-d')) }}" required>

                @error('date')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group @error('loan_holder_id') has-error @enderror">
            <label class="control-label col-sm-3 required">Loan Holder:</label>
            <div class="col-sm-9">
                <select name="loan_holder_id" id="loan_holder_id" class="form-control select2" required
                    onchange="getBalance(0)">
                    <option value="">Select Loan Holder</option>
                    @php($loan_holder_id = old('loan_holder_id', isset($data) ? $data->loan_holder_id : ''))
                    @foreach ($loanHolders as $item)
                        <option value="{{ $item->id }}" {{ $loan_holder_id == $item->id ? 'selected' : '' }}>
                            {{ $item->name . ' - ' . $item->mobile . ' - ' . $item->address }}</option>
                    @endforeach
                </select>

                @error('loan_holder_id')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label col-sm-3">Balance:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="balance" readonly>
            </div>
        </div>

        <div class="form-group @error('note') has-error @enderror">
            <label class="control-label col-sm-3">Note :</label>
            <div class="col-sm-9">
                <textarea type="text" class="form-control" name="note" rows="4">{{ old('note', isset($data) ? $data->note : '') }}</textarea>

                @error('note')
                    <span class="help-block">{{ $message }}</span>
                @enderror
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
                        <th>Final Bank Balance</th>
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
                                <select class="form-control select2" id="bank_id{{ $key }}" name="bank_id[]" required onchange="getBankBalance({{ $key }})">
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
                                <input type="number" step="any" min="1" class="form-control" id="amount{{ $key }}" name="amount[]" value="{{ $item->amount }}" onchange="checkAmount({{ $key }})" onkeyup="checkAmount({{ $key }})" required />
                            </td>
                            <td>
                                <input type="number" class="form-control" id="final_bank_balance{{ $key }}" readonly />
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td class="text-right" colspan="3">
                            <strong>Total:</strong>
                        </td>
                        <td class="text-right">
                            <input type="text" class="form-control" id="total_amount" name="total_amount" value="{{ isset($data) ? $items->sum('amount') : '' }}" readonly />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="text-right" colspan="3">
                            <strong>Final Balance:</strong>
                        </td>
                        <td class="text-right">
                            <input type="text" class="form-control" id="final_balance" readonly />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-group text-center">
            <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
            <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        function getBalance(isEdit) {
            let id = $('#loan_holder_id').val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                url: "{{ route('admin.user.loanholders.due') }}",
                beforeSend: () => {
                    $('#balance').val('');
                },
                success: (res) => {
                    if (res.success) {
                        if (isEdit) {
                            let editAmount = {{ isset($edit) ? $data->amount : 0 }}
                            let type = $('#type').val();
                            let totalAmount = Number($('#total_amount').val());
                            if (type == 'Received') {
                                let due = (Number(res.due) + editAmount);
                                $('#balance').val(due);
                            } else {
                                let due = (Number(res.due) - editAmount);
                                $('#balance').val(due);
                            }
                            totalCal();

                        } else {
                            $('#balance').val(res.due);
                            calAll();
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

        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var bankOptions = $('#bank_id' + key).html();

            var html = `<tr class="subRow" id="row${newKey}">
            <input type="hidden" name="transaction_id[]">
            <td>
                <a class="btn btn-danger btn-flat" onclick="removeRow(${newKey})"><i class="fa fa-minus"></i></a>
            </td>
            <td>
                <select class="form-control select2" id="bank_id${newKey}" name="bank_id[]" required onchange="getBankBalance(${newKey})">${bankOptions}</select>
            </td>
            <td>
                <input type="number" class="form-control" id="bank_balance${newKey}" readonly />
            </td>
            <td>
                <input type="number" step="any" min="1" class="form-control" id="amount${newKey}" name="amount[]" onchange="checkAmount(${newKey})" onkeyup="checkAmount(${newKey})" required />
            </td>
            <td>
                <input type="number" class="form-control" id="final_bank_balance${newKey}" readonly />
            </td>
        </tr>`;

            $('#responseHtml').append(html);
            $(`#bank_id${newKey}`).val('');
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select',
                tag: true
            });
        }

        function removeRow(key) {
            $(`#row${key}`).remove();
            calAll();
        }

        function getBankBalance(key, isEdit) {
            let flag = true;
            let bankId = $('#bank_id' + key).val();
            var rowId = $(".subRow").length;
            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#bank_id' + x).val() == bankId) {
                        $('#bank_id' + key).val('');
                        alerts('This bank already exists in this payment!!');
                        flag = false;
                        return false;
                    }
                }
            }

            if (flag) {
                $.ajax({
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        id: bankId
                    },
                    url: "{{ route('admin.basic.bank.due') }}",
                    beforeSend: () => {
                        $(`#bank_balance${key}`).val('');
                    },
                    success: (res) => {
                        if (res.success) {
                            let due = Number(res.due);
                            let amount = 0;
                            if (isEdit) {
                                let type = $('#type').val();
                                amount = Number($(`#amount${key}`).val());
                                due = (type == 'Payment' ? (due + amount) : (due - amount));
                            }
                            $(`#bank_balance${key}`).val(due);
                            $(`#final_bank_balance${key}`).val(due - amount);
                        } else {
                            alert(res.message);
                        }
                    },
                    error: (res) => {
                        alert(res.message);
                    }
                });
            }
        }

        function checkAmount(key) {
            let type = $('#type').val();
            let amount = $(`#amount${key}`).val();
            if (isNaN(amount)) {
                $(`#amount${key}`).val('');
                $(`#amount${key}`).focus();
                $(`#final_bank_balance${key}`).val('');
                alerts('Please Provide Valid Amount!');
            }

            let bankBalance = Number($(`#bank_balance${key}`).val());
            let finalBankBalance = (type == 'Payment' ? (bankBalance - amount) : (bankBalance + amount));
            if (finalBankBalance < 0) {
                $(`#amount${key}`).val('');
                $(`#final_bank_balance${key}`).val('');
                alert('Final Bank balance will not be less than zero!');
            } else {
                $(`#final_bank_balance${key}`).val(finalBankBalance);
            }

            calAll();
        }

        function calAll() {
            let type = $('#type').val();
            let rowCount = $("tr[id^='row']").length;
            for (let key = 0; key < rowCount; key++) {
                let bankBalance = Number($(`#bank_balance${key}`).val());
                let amount = Number($(`#amount${key}`).val());

                let finalBankBalance = (type == 'Payment' ? (bankBalance - amount) : (bankBalance + amount));

                if (finalBankBalance >= 0) {
                    $(`#final_bank_balance${key}`).val(finalBankBalance);
                } else {
                    $(`#final_bank_balance${key}`).val(bankBalance);
                    $(`#amount${key}`).val('');
                }
            }

            totalCal();
        }

        function totalCal() {
            let type = $('#type').val();
            let balance = Number($('#balance').val());

            var total_amount = 0;
            $('input[id^="amount"]').each(function() {
                total_amount += +Number($(this).val());
            });
            $('#total_amount').val(total_amount);

            var final_balance = (type == 'Payment' ? (balance + total_amount) : (balance - total_amount));
            $('#final_balance').val(Number(final_balance.toFixed(2)));
        }

        function getBankBalanceForEdit() {
            var rowCount = $("tr[id^='row']").length;
            for (var x = 0; x < rowCount; x++) {
                checkBalanceForEdit(x);
            }
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
                    $('#bank_balance' + key).val('');
                },
                success: (res) => {
                    if (res.success) {
                        $('#bank_balance' + key).val(res.due);
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
            let due = Number($('#bank_balance' + key).val());
            let amount = Number($('#amount' + key).val());
            $('#final_bank_balance' + key).val(due);
            if (type == 'Payment') {
                due = due + amount;
            } else {
                due = due - amount;
            }
            $('#bank_balance' + key).val(due);

        }

        @if (isset($edit))
            getBalance(1);
            getBankBalanceForEdit();
        @endif
    </script>
@endpush