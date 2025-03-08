<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" class="form-horizontal non-validate" id="add_customer_form" onsubmit="return customerFormSubmit(event)">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="customerModalLabel">Add Customer</h4>
                </div>
                <div class="modal-body">
                    @if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                        <div class="form-group">
                            <label class="control-label col-sm-3 required">Type:</label>
                            <div class="col-sm-9">
                                <select name="type" class="form-control select2" required id="customer_type">
                                    @foreach (['Admin', 'Seller', 'Reseller'] as $sts)
                                        <option value="{{ $sts }}">{{ $sts }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="help-block" id="error_type"></span>
                            </div>
                        </div>    
                        <div class="form-group" id="customer_user" style="display: none">
                            <label class="control-label col-sm-3">Seller or Reseller:</label>
                            <div class="col-sm-9">
                                <select name="user_id" class="form-control select2" id="customer_user_id">
                                    <option value=""> Select Seller or Reseller</option>
                                </select>
                                <span class="help-block" id="error_seller_reseller"></span>
                            </div>
                        </div>    
                    @endif

                    <div class="form-group">
                        <label class="control-label col-sm-3 required">Name:</label>
                        <div class="col-sm-9">
                            <input type="text" id="name" class="form-control" name="name" value="{{ old('name') }}" required>
                            <span class="help-block" id="error_name"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3 ">Contact Person:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="contact_person" name="contact_person"
                                value="{{ old('contact_person') }}">
                            <span class="help-block" id="error_contact_person"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 required">Contact Number:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile') }}" required>
                            <span class="help-block" id="error_mobile"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Email:</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                            <span class="help-block" id="error_email"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">Address:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                            <span class="help-block" id="error_address"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Shipping Address:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="shipping_address"
                                value="{{ old('shipping_address') }}" id="shipping_address">
                            <span class="help-block" id="error_shipping_address"></span>
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <label class="control-label col-sm-3">Shipping Method:</label>
                        <div class="col-sm-9">
                            <select name="shipping_rate_id" class="form-control select2" id="shipping_rate_id">
                                <option value=""> Select Shipping Method</option>
                                @foreach ($shipping_methods as $shipping_method)
                                    <option value="{{ $shipping_method->id }}">{{ $shipping_method->name }}</option>
                                @endforeach
                            </select>
                            <span class="help-block" id="error_shipping_rate_id"></span>
                        </div>
                    </div> --}}
                    <div class="form-group">
                        <label class="control-label col-sm-3 required">Status:</label>
                        <div class="col-sm-9">
                            <select name="status" class="form-control" required id="status">
                                @foreach (['Active', 'Deactivated'] as $sts)
                                    <option value="{{ $sts }}">{{ $sts }}</option>
                                @endforeach
                            </select>
                            <span class="help-block" id="error_status"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="customerSubmit" class="btn btn-success btn-flat">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
   $('#customer_type').on('change', function(){
        let role = $(this).val();
        if ( role == "Admin") {
            $('#customer_user').hide();
        } else {
                $('#customer_user').show();
                $.ajax({
                url: '{{ route('admin.user.getuser') }}',
                type: "GET",
                dataType: 'json',
                data: {
                    role: role,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var html = '';
                    $( response.data ).each(function( index , val ) {
                        html += ` <option value="${val.id}"> ${val.name}</option> `;
                    });
                    $('#customer_user_id').html(html);
                }
            })
        }
    });
    $(document).on('submit', '#add_customer_form',  function(event) {
        event.preventDefault();
        var formElement = $(this).serializeArray()
        var formData = new FormData();
        formElement.forEach(element => {
            formData.append(element.name, element.value);
        });
        formData.append('_token', "{{ csrf_token() }}");
        resetValidationErrors();
        $.ajax({
            url: "{{ route('admin.user.customers.ajax-store') }}",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function(response) {
                alert(response.successMessage);
                resetForm();
                $('#customerModal').modal('hide');
            },
            error: function(response) {
                showValidationErrors('#add_customer_form', response.responseJSON.errors);
            }
        });
    });
    function showValidationErrors(formType, errors) { 
        $(formType +' #error_type').text(errors.type);
        $(formType +' #error_seller_reseller').text(errors.user_id);
        $(formType +' #error_name').text(errors.name);
        $(formType +' #error_contact_person').text(errors.contact_person);
        $(formType +' #error_mobile').text(errors.mobile);
        $(formType +' #error_email').text(errors.email);
        $(formType +' #error_address').text(errors.address);
        $(formType +' #error_shipping_address').text(errors.shipping_address);
        $(formType +' #error_shipping_rate_id').text(errors.shipping_rate_id);
        $(formType +' #error_status').text(errors.status);
    }
    function resetValidationErrors(){
        $('#error_type').text('');
        $('#error_seller_reseller').text('');
        $('#error_name').text('');
        $('#error_contact_person').text('');
        $('#error_mobile').text('');
        $('#error_email').text('');
        $('#error_address').text('');
        $('#error_shipping_address').text('');
        $('#error_shipping_rate_id').text('');
        $('#error_status').text('');
    }
    function resetForm(){
        $("#type").select2().val('').trigger("change");
        $("#customer_type").select2().val('').trigger("change");
        $("#user_id").select2().val('').trigger("change");
        $("#customer_user_id").select2().val('').trigger("change");
        $("#shipping_rate_id").select2().val('').trigger("change");
        $('#name').val('');
        $('#contact_person').val('');
        $('#mobile').val('');
        $('#email').val('');
        $('#address').val('');
        $('#shipping_address').val('');
        $('#status').val('');
    }
</script>
@endpush