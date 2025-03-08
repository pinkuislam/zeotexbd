<input type="hidden" name="type" value="Adjustment">
<div class="row">
    <div class="col-sm-12">
        <div class="form-group @error('date') has-error @enderror">
            <label class="control-label col-sm-3 required">Date :</label>
            <div class="col-sm-9">
                <input type="text" class="form-control datepicker" name="date"
                    value="{{ old('date', isset($data) ? $data->date : date('Y-m-d')) }}" required>

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

        <div class="form-group @error('total_amount') has-error @enderror">
            <label class="control-label col-sm-3 required">Amount :</label>
            <div class="col-sm-9">
                <input type="number" step="any" min="0" class="form-control" id="total_amount"
                    name="total_amount" value="{{ old('total_amount', isset($data) ? $data->amount : '') }}" required
                    onkeyup="checkAmount()">

                @error('total_amount')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Final Balance:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="final_balance" readonly>
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

        <div class="form-group text-center">
            <button type="submit"
                class="btn btn-success btn-flat btn-lg">{{ isset($data) ? 'Update' : 'Create' }}</button>
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
            data: { id: id },
            url: "{{ route('admin.user.loanholders.due') }}",
            beforeSend: () => {
                $('#balance').val('');
            },
            success: (res) => {
                if (res.success) {
                    if (isEdit) {
                        let totalAmount = Number($('#total_amount').val());
                        let due = (Number(res.due) - totalAmount);
                        $('#balance').val(due);
                        $('#final_balance').val(res.due);
                    } else {
                        $('#balance').val(res.due);
                        $('#final_balance').val(res.due);
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

    function checkAmount() {
        let totalAmount = Number($(`#total_amount`).val());
        if (isNaN(totalAmount)) {
            $(`#total_amount`).val('');
            $(`#total_amount`).focus();
            alerts('Please Provide Valid Amount!');
        }

        let balance = Number($(`#balance`).val());
        let finalBalance = (balance + totalAmount);
        if (finalBalance < 0) {
            $(`#total_amount`).val('');
            $(`#final_balance`).val('');
            alert('Final balance will not be less than zero!');
        } else {
            $(`#final_balance`).val(finalBalance);
        }
    }

    @if (isset($data))
        getBalance(1);
    @endif
</script>
@endpush
