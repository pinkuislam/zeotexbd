@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.payment.customer-payments.index') . qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Customer Payment List
                </a>
            </li>

            @can('add customer-payment')
            <li {{ $type == 'Received' ? 'class=active' : '' }}>
                <a href="{{ route('admin.payment.customer-payments.receive') . qString() }}">
                    <i class="fa fa-{{ $type == 'Received' ? 'edit' : 'plus' }}" aria-hidden="true"></i> {{ $type == 'Received' ? 'Edit' : 'Add' }} Receive
                </a>
            </li>
            <li {{ $type == 'Payment' ? 'class=active' : '' }}>
                <a href="{{ route('admin.payment.customer-payments.create') . qString() }}">
                    <i class="fa fa-{{ $type == 'Payment' ? 'edit' : 'plus' }}" aria-hidden="true"></i> {{ $type == 'Payment' ? 'Edit' : 'Add' }} Payment
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
                    <form method="POST" action="{{ route('admin.payment.customer-payments.update', $data->id).qString() }}" id="are_you_sure" class="form-horizontal">
                        @csrf

                        @method('PUT')
                        
                        @include('admin.payment.customer.form', ['type' => $type])
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
