<div class="tab-pane active">
    <div class="box-body">
        <form method="POST" action="{{ route('admin.asset.asset-items.update', $edit) }}{{ qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                        <label class="control-label col-sm-3 required">Date :</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control datepicker" name="date"
                                value="{{ old('date', dbDateRetrieve($data->date)) }}"
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
                                value="{{ old('note', $data->note) }}">

                            @if ($errors->has('note'))
                                <span class="help-block">{{ $errors->first('note') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="required">Asset Name</th>
                            <th class="required">Quantity</th>
                            <th class="required">Price</th>
                            <th class="required">Total Price</th>
                        </tr>
                    </thead>
                    <tbody id="responseHtml">
                        <tr class="subRow">
                            <td>
                                <input type="text" class="form-control" id="asset_id" value="{{ $data->asset != null ? $data->asset->name : '-' }}" readonly>
                                <input type="hidden" class="form-control" name="asset_id" value="{{ $data->asset_id }}">
                            </td>
                            <td>
                                <input type="number" class="form-control" name="quantity"
                                    id="edit_quantity"
                                    value="{{ number_format($data->quantity, 2) }}"
                                    onclick="editchkItemPrice()"
                                    onkeyup="editchkItemPrice()" required>
                            </td>
                            <td>
                                <input type="number" class="form-control"
                                    name="price" id="edit_price"
                                    value="{{ number_format($data->price, 2) }}"
                                    onkeyup="editchkItemPrice()" onclick="editchkItemPrice()" required>
                            </td>
                            <td>
                                <input type="number" class="form-control" name="amount" id="edit_amount" value="{{ $data->total_amount }}" readonly>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <div class="text-center">
                    <button type="submit"
                        class="btn btn-success btn-flat">{{ __('Update') }}</button>
                    <button type="reset"
                        class="btn btn-warning btn-flat">{{ __('Clear') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    <script>
        function editchkItemPrice() {
            var quantity = Number($('#edit_quantity').val());
            var price = Number($('#edit_price').val());

            var total = Number(quantity * price);
            $('#edit_amount').val(total);
        }
</script>
@endpush