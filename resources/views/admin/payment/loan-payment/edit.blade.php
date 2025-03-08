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
                    <i class="fa fa-edit" aria-hidden="true"></i> Edit Loan Payment
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.payment.loan-payments.update', $data->id).qString() }}" id="are_you_sure" class="form-horizontal">
                        @csrf
                        @method('PUT')

                        @if (isset($adjustment) && $adjustment == true)
                            @include('admin.payment.loan-payment.form-adjustment')
                        @else
                            @include('admin.payment.loan-payment.form')
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
