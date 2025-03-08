<div class="modal fade" id="deliveryModal" tabindex="-1" role="dialog" aria-labelledby="deliveryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" class="form-horizontal non-validate" id="add_delivery_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deliveryModalLabel">Update Delivery</h4>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id" value="{{ $data->id}}">
                    {{-- <div class="form-group">
                        <label class="control-label col-sm-3 required">Delivery Status:</label>
                        <div class="col-sm-9">
                            <select name="status" class="form-control select2" required id="status">
                                @php($status = old('status', isset($data) ? $data->status : ''))
                                @foreach (['Pending','Processing','Shipped','Delivered'] as $sts)
                                    <option value="{{ $sts }}" {{ $status == $sts ? 'selected' : '' }}>{{ $sts }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="error_status"></span>
                        </div>
                    </div> --}}
                    
                    <div class="form-group">
                        <label class="control-label col-sm-3 required">Total Amount:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="total_amount" name="total_amount"
                            value="{{ number_format($due, 2) }}" readonly>
                            <span class="text-danger" id="error_total_amount"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 required">Payment Amount:</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="payment_amount" name="payment_amount"
                            value="{{ old('payment_amount') ?? 0 }}" required>
                            <span class="text-danger" id="error_payment_amount"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Adj. Amount:</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="adjustment_amount" name="adjustment_amount"
                            value="{{ old('adjustment_amount') ?? 0 }}">
                            <span class="text-danger" id="error_adjustment_amount"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 required">Delivery Agent :</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" name="delivery_agent_id" required id="delivery_agent_id">
                                <option value="">Select Delivery Agent</option>
                                @php($delivery_agent_id = old('delivery_agent_id', isset($data) ? $data->delivery_agent_id : ''))
                                @foreach ($delivery_agents as $delivery_agent)
                                    <option value="{{ $delivery_agent->id }}"
                                        {{ $delivery_agent_id == $delivery_agent->id ? 'selected' : '' }}>
                                        {{ $delivery_agent->name }} </option>
                                @endforeach
                            </select>
                            @if ($errors->has('delivery_agent_id'))
                                <span class="text-danger">{{ $errors->first('delivery_agent_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Shipping Charge:</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="shipping_charge" name="shipping_charge"
                            value="{{ old('shipping_charge') ?? $data->shipping_charge }}" readonly>
                            <span class="text-danger" id="error_shipping_charge"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Extra Shipping Charge:</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="extra_shipping_charge" name="extra_shipping_charge"
                            value="{{ old('extra_shipping_charge') ?? $data->extra_shipping_charge }}">
                            <span class="text-danger" id="error_extra_shipping_charge"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 required">Bank:</label>
                        <div class="col-sm-9">
                            <select name="bank" class="form-control select2" required id="bank">
                                @php($status = old('bank'))
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ $status == $bank->id ? 'selected' : '' }}>{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="error_bank"></span>
                        </div>
                    </div>
                  
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-flat">Update Delivery</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    $(document).on('submit', '#add_delivery_form',  function(event) {
        event.preventDefault();
        var formElement = $(this).serializeArray()
        var formData = new FormData();
        formElement.forEach(element => {
            formData.append(element.name, element.value);
        });
        formData.append('_token', "{{ csrf_token() }}");
        $.ajax({
            url: "{{ route('admin.sale.delivery.ajax-store') }}",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function(response) {
                alert(response.successMessage);
                location.reload();
            },
            error: function(response) {
                alert('error');
            }
        });
    });

</script>
@endpush