@extends('layouts.app')

@section('content')
@if($errors->any())
    <section class="content-header">
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {!! implode('', $errors->all('<div>:message</div>')) !!}
        </div>
    </section>
@endif
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.payment.customer-payments.index') . qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Customer Payment List
                </a>
            </li>

            @can('add customer-payment')
            <li>
                <a href="{{ route('admin.payment.customer-payments.receive') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Receive
                </a>
            </li>
            <li class="active">
                <a href="{{ route('admin.payment.customer-payments.bulk-receive') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Bulk Receive
                </a>
            </li>
            <li>
                <a href="{{ route('admin.payment.customer-payments.create') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Payment
                </a>
            </li>
            <li>
                <a href="{{ route('admin.payment.customer-payments.adjustment') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                </a>
            </li>
            @endcan
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.payment.customer-payments.bulk-receive.store').qString() }}" id="are_you_sure" class="form-horizontal">
                        @csrf

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label for="orderSearch" class="control-label col-sm-3">Search Order:</label>
                                    <div class="col-sm-9">
                                        <div class="">
                                            <div class="col-sm-10" style="padding: 0 !important">
                                                <input type="text" v-model="order_search" id="orderSearch" class="form-control" placeholder="Search Order">
                                            </div>
                                            <div class="col-sm-2">
                                                <a @click="getOrder" class="btn btn-info">Search Order</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                    <label for="date" class="control-label col-sm-3 required">Date :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control datepicker" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>

                                        @if ($errors->has('date'))
                                            <span class="help-block">{{ $errors->first('date') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="box-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Code</th>
                                            <th>Order Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Due Amount</th>
                                            <th class="required">Pay Amount</th>
                                            <th>Final Order Due</th>
                                            <th>Delivered</th>
                                            <th>Agent</th>
                                            <th>Charge</th>
                                        </tr>
                                        </thead>

                                        <tbody id="responseHtml">
                                        <tr v-for="(item, index) in items">
                                            <td><a class="btn btn-danger btn-flat" @click="removeItem(index)"><i class="fa fa-minus"></i></a></td>
                                            <td>
                                                <input type="hidden" name="order_id[]" :value="item.order_id" />
                                                <input type="text" class="form-control" :value="item.code" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" :value="item.total_amount" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" :value="item.customer_pay" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" :value="item.order_due" readonly />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="amount[]" v-model="items[index].amount" />
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" :value="item.final_order_due" readonly />
                                            </td>
                                            <td class="text-center">
                                                <label class="form-control"><input type="checkbox" name="delivered[]" :checked="item.delivered" /></label>
                                            </td>
                                            {{-- Ferdous correction --}}
                                            <td class="text-right">
                                                <select name="delivery_agent_id[]" class="form-control select2" v-model="items[index].delivery_agent_id" required>
                                                    <option value="0">Agent</option>
                                                    @foreach ($agents as $agent)
                                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="delivery_charge[]" v-model="items[index].delivery_charge" />
                                            </td>
                                        </tr>
                                        </tbody>

                                        <tfoot>
                                        <tr>
                                            <td class="text-right" colspan="5">
                                                <strong>Total:</strong>
                                            </td>

                                            <td class="text-right">
                                                <input type="text" class="form-control" readonly name="total_amount" id="total_amount" :value="total_amount">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right" colspan="5">
                                                <label for="bank_id">Bank:</label>
                                            </td>

                                            <td class="text-right">
                                                <select name="bank_id" id="bank_id" class="form-control select2" required>
                                                    <option value="">Select Bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div style="margin-top: 5rem;" class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                    <label class="control-label col-sm-3">Note :</label>
                                    <div class="col-sm-9">
                                        <textarea type="text" class="form-control" name="note" rows="4">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
                                        @if ($errors->has('note'))
                                            <span class="help-block">{{ $errors->first('note') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-flat btn-lg">Create</button>
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
    <script src="https://cdn.jsdelivr.net/npm/vue@2.7.16"></script>
    <script>
        new Vue({
            el: "#are_you_sure",
            data: {
                order_search: '',
                items: [],
            },
            computed: {
                total_amount: function () {
                    return this.items.reduce(function (acc, item) {
                        return acc + Number(item.amount || 0);
                    }, 0);
                },
            },
            watch: {
                items: {
                    handler: function () {
                        this.items.forEach((item, index) => {
                           this.$set(this.items[index], 'final_order_due', (item.order_due - item.amount));
                        });
                    },
                    deep: true,
                },
            },
            methods: {
                getOrder() {
                    if (this.items.find(i => i.code === this.order_search)) {
                        alert('Order code already exist!');
                        return;
                    }
                    $.ajax({
                        type: 'GET',
                        dataType: 'JSON',
                        url: "{{ route('admin.payment.order') }}",
                        data: {
                            code: this.order_search,
                        },
                        success: (res) => {
                            if (res.success) {
                                console.log(res);
                                res.data.code = this.order_search;
                                res.data.amount = '';
                                this.items.push(res.data);
                            } else {
                                alert(res.message);
                            }
                            this.order_search = '';
                        },
                        error: (res) => {
                            alert(res.message);
                        }
                    });
                },
                removeItem(index) {
                    this.$delete(this.items, index);
                },
            },
        });
    </script>
@endpush
