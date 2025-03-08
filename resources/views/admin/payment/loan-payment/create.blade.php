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
            <li @if (!isset($adjustment)) class="active" @endif>
                <a href="{{ route('admin.payment.loan-payments.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Loan Payment
                </a>
            </li>
                    
            <li @if (isset($adjustment) && $adjustment == true) class="active" @endif>
                <a href="{{ route('admin.payment.loan-payments.adjustment') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                </a>
            </li>
            @endcan
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.payment.loan-payments.store').qString() }}" id="are_you_sure" class="form-horizontal">
                        @csrf
                        
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
