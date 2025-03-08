<div class="row">
    <div class="col-sm-8">
        <div class="form-group{{ $errors->has('from_bank_id') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 required">Bank (From):</label>
            <div class="col-sm-9">
                <select name="from_bank_id" id="from_bank_id" class="form-control select2" required onchange="getFromDue(0)">
                    <option value="">Select Bank</option>
                    @php($from_bank_id = old('from_bank_id', isset($data) ? $data->from_bank_id : ''))
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}" {{ $from_bank_id == $bank->id ? 'selected' : '' }}>
                            {{ $bank->bank_name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('from_bank_id'))
                    <span class="help-block">{{ $errors->first('from_bank_id') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Balance:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="from_due" readonly>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Final Balance:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="from_final_balance" readonly>
            </div>
        </div>

        <div class="form-group{{ $errors->has('to_bank_id') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 required">Bank (To):</label>
            <div class="col-sm-9">
                <select name="to_bank_id" id="to_bank_id" class="form-control select2" required onchange="getToDue(0)">
                    <option value="">Select Bank</option>
                    @php($to_bank_id = old('to_bank_id', isset($data) ? $data->to_bank_id : ''))
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}" {{ $to_bank_id == $bank->id ? 'selected' : '' }}>
                            {{ $bank->bank_name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('to_bank_id'))
                    <span class="help-block">{{ $errors->first('to_bank_id') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Balance:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="to_due" readonly>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Final Balance:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="to_final_balance" readonly>
            </div>
        </div>

        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 required">Date:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control datepicker" name="date" value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : (Session::has('payDate') ? Session::get('payDate') : date('Y-m-d'))) }}" required>

                @if ($errors->has('date'))
                    <span class="help-block">{{ $errors->first('date') }}</span>
                @endif
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

        <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
            <label class="control-label col-sm-3 required">Amount:</label>
            <div class="col-sm-9">
                <input type="number" step="any" min="0" class="form-control" name="amount" id="amount" value="{{ old('amount', isset($data) ? $data->amount : '') }}" required onchange="checkAmountCost()" onkeyup="checkAmountCost()">

                @if ($errors->has('amount'))
                    <span class="help-block">{{ $errors->first('amount') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 text-center">
                <button type="submit" class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Submit' }}</button>
                <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function getFromDue(isEdit) {
            let id = $('#from_bank_id').val();
            let to_id = $('#to_bank_id').val();
            if (id == to_id) {
                alert('Same Bank Transfer Not Allowed!');
                $('#from_bank_id').val('');
            } else {
                $.ajax({
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        id: id
                    },
                    url: "{{ route('admin.basic.bank.due') }}",
                    beforeSend: () => {
                        $('#from_due').val('');
                    },
                    success: (res) => {
                        if (res.success) {
                            let due = Number(res.due);
                            if(isEdit){
                                let amount = Number($('#amount').val());
                                let fromFinalBalance = due;
                                    due = (due + amount);
                                fromFinalBalance = (due - amount);
                                $('#from_due').val(due);
                                $('#from_final_balance').val(fromFinalBalance);
                            } else {
                                $('#from_due').val(due);
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
        }

        function getToDue(isEdit) {
            let id = $('#to_bank_id').val();
            let from_id = $('#from_bank_id').val();
            if (id == from_id) {
                alert('Same Bank Transfer Not Allowed!');
                $('#to_bank_id').val('');
            } else {
                $.ajax({
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        id: id
                    },
                    url: "{{ route('admin.basic.bank.due') }}",
                    beforeSend: () => {
                        $('#to_due').val('');
                    },
                    success: (res) => {
                        if (res.success) {
                            let due = Number(res.due);
                            if(isEdit){
                                let amount = Number($('#amount').val());
                                let toFinalBalance = due;
                                    due = (due - amount);
                                toFinalBalance = (due + amount);
                                $('#to_due').val(due);
                                $('#to_final_balance').val(toFinalBalance);
                            } else {
                                $('#to_due').val(due);
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
        }

        function remainingDue() {
            let from_due = Number($('#from_due').val());
            let to_due = Number($('#to_due').val());
            let amount = Number($('#amount').val());
            if (from_due < amount) {
                alert('Transfer amount could not be greater then from bank blance!');
                $('#amount').val('');
                amount = 0;
            }
            let fromFinalBalance = (from_due - amount);
            let toFinalBalance = (to_due + amount);

            $('#from_final_balance').val(fromFinalBalance);
            $('#to_final_balance').val(toFinalBalance);
        }

        @if (isset($data))
            getFromDue(1);
            getToDue(1);
            setTimeout(function() {
                remainingDue();
            }, 2000);
        @endif

        function checkAmountCost() {
            let amount = $('#amount').val();
            if (isNaN(amount)) {
                $('#amount').val('');
                $('#amount').focus();
                alerts('Please Provide Valid Amount!');
            }
            remainingDue();
        }
    </script>
@endpush