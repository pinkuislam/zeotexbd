@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.payment.supplier-payments.index') . qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Supplier Payment List
                </a>
            </li>

            @can('add supplier-payment')
            <li {{ $type == 'Payment' ? 'class=active' : '' }}>
                <a href="{{ route('admin.payment.supplier-payments.create') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Payment
                </a>
            </li>
            <li {{ $type == 'Received' ? 'class=active' : '' }}>
                <a href="{{ route('admin.payment.supplier-payments.receive') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Receive
                </a>
            </li>
            <li>
                <a href="{{ route('admin.payment.supplier-payments.adjustment') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                </a>
            </li>
            @endcan
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.payment.supplier-payments.store').qString() }}" id="are_you_sure" class="form-horizontal">
                        @csrf
                        
                        @include('admin.payment.supplier.form', ['type' => $type])
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
