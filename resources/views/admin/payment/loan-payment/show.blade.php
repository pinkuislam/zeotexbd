@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.payment.loan-payments.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Loan Payment List
                </a>
            </li>

            @can('add loan')
            <li>
                <a href="{{ route('admin.payment.loan-payments.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Loan Payment
                </a>
            </li>
                    
            <li>
                <a href="{{ route('admin.payment.loan-payments.adjustment') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                </a>
            </li>
            @endcan

            <li class="active">
                <a href="javascript:void(0);">
                    <i class="fa fa-eye" aria-hidden="true"></i> Loan Payment Details
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width:120px;">Loan Holder</th>
                                <th style="width:10px;">:</th>
                                <td>{{ optional($data->loanHolder)->name }}</td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <th>:</th>
                                <td>{{ dateFormat($data->date) }}</td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <th>:</th>
                                <td>{{ $data->type }}</td>
                            </tr>
                            <tr>
                                <th>Amount</th>
                                <th>:</th>
                                <td>{{ $data->amount }}</td>
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
                                <th>Created At</th>
                                <th>:</th>
                                <td>{{ dateFormat($data->created_at, 1) }}</td>
                            </tr>                            
                            <tr>
                                <th>Updated By</th>
                                <th>:</th>
                                <td>{{ $data->updater->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <th>:</th>
                                <td>{{ dateFormat($data->updated_at, 1) }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    @if ($data->transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Bank</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->transactions as $key => $item)
                                    <tr>
                                        <td>{{ ($key + 1) }}</td>
                                        <td>{{ $item->bank->bank_name ?? '-'  }}</td>
                                        <td style="text-align: right;">{{ $item->amount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th style="text-align: right;">{{ $data->amount }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
