@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.payment.expense.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Expense List
                    </a>
                </li>
                @can('add expense')
                    <li {{ isset($create) ? 'class=active' : '' }}>
                        <a href="{{ route('admin.payment.expense.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Expense
                        </a>
                    </li>
                @endcan
                @can('edit expense')
                    @if (isset($edit))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-edit" aria-hidden="true"></i> Edit Expense
                            </a>
                        </li>
                    @endif
                @endcan
                @can('show expense')
                    @if (isset($show))
                        <li class="active">
                            <a href="#">
                                <i class="fa fa-list-alt" aria-hidden="true"></i> Expense Details
                            </a>
                        </li>
                    @endif
                @endcan
            </ul>

            <div class="tab-content">
                @if (isset($show))
                    <div class="tab-pane active">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:120px;">Expense Date.</th>
                                    <th style="width:10px;">:</th>
                                    <td>{{ dateFormat($data->date) }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <th>:</th>
                                    <td>{!! nl2br($data->note) !!}</td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <th>:</th>
                                    <td>{{ $data->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <th>:</th>
                                    <td>{{ $data->updater->name ?? '-' }}</td>
                                </tr>
                            </table>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Category</th>
                                        <th>Bank</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->items as $key => $item)
                                        <tr>
                                            <td>{{$key + 1 }}</td>
                                            <td>{{ optional($item->category)->name }}</td>
                                            <td>{{ optional($item->bank)->bank_name }}</td>
                                            <td style="text-align: right;">{{ $item->amount }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th style="text-align: right;" colspan="3">Total Amount :</th>
                                        <th style="text-align: right;">{{ numberFormat($data->items->sum('amount')) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @elseif(isset($edit) || isset($create))
                    <div class="tab-pane active">
                        <div class="box-body">
                            <form method="POST"
                                action="{{ isset($edit) ? route('admin.payment.expense.update', $edit) : route('admin.payment.expense.store') }}{{ qString() }}"
                                id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                                @csrf

                                @if (isset($edit))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                            <label class="control-label col-sm-3 required">Date :</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control datepicker" name="date"
                                                    value="{{ old('date', isset($data) ? dbDateRetrieve($data->date) : date('d-m-Y')) }}"
                                                    required>

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
                                    </div>
                                </div>
                                <div class="box-body table-responsive bankdiv">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th class="required">Category</th>
                                                <th class="required">Bank</th>
                                                <th class="required">Amount</th>
                                            </tr>
                                        </thead>
                        
                                        <tbody id="responseHtml">
                        
                                            @foreach ($items as $key => $item)
                                                <tr class="subRow" id="row{{ $key }}">
                                                    <input type="hidden" name="expense_item_id[]"value="{{ $item->id }}">
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
                                                        <select name="category_id[]" id="category_id{{ $key }}" onchange="checkCategory({{ $key }})"
                                                            class="form-control select2" required>
                                                            <option value="">Select category</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    {{ $item->expense_category_id == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="bank_id[]" id="bank_id{{ $key }}"
                                                            class="form-control select2" required>
                                                            <option value="">Select Bank</option>
                                                            @foreach ($banks as $bank)
                                                                <option value="{{ $bank->id }}"
                                                                    {{ $item->bank_id == $bank->id ? 'selected' : '' }}>
                                                                    {{ $bank->bank_name }}</option>
                                                            @endforeach
                                                        </select>
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
                                    <div class="text-center">
                                        <button type="submit"
                                            class="btn btn-success btn-flat">{{ isset($edit) ? 'Update' : 'Create' }}</button>
                                        <button type="reset"
                                            class="btn btn-warning btn-flat">Clear</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif (isset($list))
                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.payment.expense.index') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="from" id="datepickerFrom"
                                            value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="to" id="datepickerTo"
                                            value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" class="form-control" name="q"
                                            value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn-info btn-flat">Search</button>
                                        <a class="btn btn-warning btn-flat" href="{{ route('admin.payment.expense.index') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive-lg">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Note</th>
                                        <th>Category</th>
                                        <th>Amount (৳)</th>
                                        <th>Created By</th>
                                        <th class="col-action">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $val)
                                        <tr>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ excerpt($val->note) }}</td>
                                            <td>
                                                @foreach ($val->items as $item)
                                                    {{ optional($item->category)->name}} - {{ optional($item->bank)->bank_name}} ({{ $item->amount}}) <br>
                                                @endforeach
                                            </td>
                                            <td>{{ $val->total_amount }}</td>
                                            <td>{{ $val->creator->name ?? '-' }}</td>
                                            <td>
                                                @canany(['show expense', 'edit expense', 'delete expense'])
                                                    <div class="dropdown">
                                                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                            type="button" data-toggle="dropdown">Action <span
                                                                class="caret"></span></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            @can('show expense')
                                                                <li><a href="{{ route('admin.payment.expense.show', $val->id) . qString() }}"><i
                                                                            class="fa fa-eye"></i> Show</a></li>
                                                            @endcan
                                                            @can('edit expense')
                                                                <li><a href="{{ route('admin.payment.expense.edit', $val->id) . qString() }}"><i
                                                                            class="fa fa-pencil"></i> Edit</a></li>
                                                            @endcan
                                                            @can('delete expense')
                                                                <li><a
                                                                        onclick="deleted('{{ route('admin.payment.expense.destroy', $val->id) . qString() }}')"><i
                                                                            class="fa fa-close"></i> Delete</a></li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                                @endcanany
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($expenses) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $expenses->appends(Request::except('page'))->links() }}
                            </div>

                            <div class="col-sm-4">
                                <div class="pagi-limit-box">
                                    <div class="input-group pagi-limit-box-body">
                                        <span class="input-group-addon">Show:</span>

                                        <select class="form-control pagi-limit" name="limit">
                                            @foreach (paginations() as $pag)
                                                <option value="{{ qUrl(['limit' => $pag]) }}"
                                                    {{ $pag == Request::get('limit') ? 'selected' : '' }}>
                                                    {{ $pag }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function addRow(key) {
            var newKey = $("tr[id^='row']").length;
            var categoryoptions = $('#category_id' + key).html();
            var bankoptions = $('#bank_id' + key).html();

            var html = `<tr class="subRow" id="row` + newKey + `">
            <td><a class="btn btn-danger btn-flat" onclick="removeRow(` + newKey + `)"><i class="fa fa-minus"></i></a></td>
            <input type="hidden" name="expense_item_id[]" value="0">
            <td>
                <select name="category_id[]" id="category_id` + newKey + `" class="form-control select2" onchange="checkCategory(` + newKey + `)" required>` + categoryoptions + `</select>
            </td>
            <td>
                <select name="bank_id[]" id="bank_id` + newKey + `" class="form-control select2" required>` + bankoptions + `</select>
            </td>
            <td>
                <input type="number" step="any" min="1" class="form-control amount" name="amount[]" id="amount` + newKey +
                `" onkeyup="checkAmountCost(` + newKey + `)" onclick="checkAmountCost(` + newKey + `)" required>
            </td>
        </tr>`;
        $('#responseHtml').append(html);
        $('#category_id' + newKey).val('');
            $('#bank_id' + newKey).val('');
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select',
                tag: true
            });
        }

        function removeRow(key) {
            $('#row' + key).remove();
        }
        function checkAmountCost(key) {
            
            let amount = Number($('#amount' + key).val());

            if (isNaN(amount)) {
                $('#amount' + key).val('');
                $('#amount' + key).focus();
                alerts('Please Provide Valid Amount!');
            }
            var total_amount = 0;
            $(".amount").each(function() {
                total_amount += Number($(this).val());
            });
            $('#total_amount').val(Number(total_amount));
        }
        
        function checkCategory(key) {
            var categoryId = $('#category_id' + key).val();
            var rowId = $(".subRow").length;
            for (var x = 0; x < rowId; x++) {
                if (x != key) {
                    if ($('#category_id' + x).val() == categoryId) {
                        $('#category_id' + key).val('');
                        alerts('This category already exists in this Expense!!');
                        return false;
                    }
                }
            }
        }
    </script>
@endpush
