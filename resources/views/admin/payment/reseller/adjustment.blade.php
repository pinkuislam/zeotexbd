@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.payment.reseller-payments.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Reseller Payment List
                    </a>
                </li>

                @can('add reseller-payment')
                <li>
                    <a href="{{ route('admin.payment.reseller-payments.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Payment
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payment.reseller-payments.receive') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Receive
                    </a>
                </li>
                <li class="active">
                    <a href="{{ route('admin.payment.reseller-payments.adjustment') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                    </a>
                </li>
                @endcan
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="box-body">
                        <form method="POST"
                            action="{{ isset($data) ? route('admin.payment.reseller-payments.update', $data->id) : route('admin.payment.reseller-payments.store') }}{{ qString() }}"
                            id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                            @csrf

                            @if (isset($data))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="hidden" class="form-control" name="type" value="Adjustment">

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

                                    <div class="form-group{{ $errors->has('reseller_id') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 required">Reseller:</label>
                                        <div class="col-sm-9">
                                            <select name="reseller_id" class="form-control select2" id="adjust_reseller_id" required onchange="getAdjustDue()">
                                                <option value="">Select Reseller</option>
                                                @php($reseller_id = old('reseller_id', isset($data) ? $data->reseller_id : ''))
                                                @foreach ($resellers as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ $reseller_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}</option>
                                                @endforeach
                                            </select>

                                            @if ($errors->has('reseller_id'))
                                                <span class="help-block">{{ $errors->first('reseller_id') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('total_amount') ? ' has-error' : '' }}">
                                        <label class="control-label col-sm-3 required">Amount:</label>
                                        <div class="col-sm-9">
                                            <input type="number" step="any" min="0" class="form-control"
                                                name="total_amount"
                                                value="{{ old('total_amount', isset($data) ? $data->total_amount : '') }}"
                                                required id="adjust_total_amount" onchange="checkAdjustAmount()"
                                                onkeyup="checkAdjustAmount()">

                                            @if ($errors->has('total_amount'))
                                                <span class="help-block">{{ $errors->first('total_amount') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!isset($data))
                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Due:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="adjust_due" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Final Balance:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="adjust_final_balance" readonly>
                                        </div>
                                    </div>
                                    @endif

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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function getAdjustDue(){
            let id = $('#adjust_reseller_id').val();
            if(id < 1){
                return;
            }
            let adjustAmount = Number($('#adjust_total_amount').val());
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                url: "{{ route('admin.user.resellers.due') }}",
                beforeSend: () => {
                    $('#adjust_due').val('');
                },
                success: (res) => {
                    if (res.success) {
                        let due = Number(res.due);
                        $('#adjust_due').val(due);
                        checkAdjustAmount();
                    } else {
                        alert(res.message);
                    }
                },
                error: (res) => {
                    alert(res.message);
                }
            });
        }

        function checkAdjustAmount(){
            let adjustDue = Number($('#adjust_due').val());
            let adjustAmount = Number($('#adjust_total_amount').val());
            let finalBalance = adjustDue - adjustAmount;
            $('#adjust_final_balance').val(finalBalance);
        }
    </script>
@endpush
