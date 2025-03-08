@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.payment.seller-payments.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Seller Payment List
                    </a>
                </li>

                @can('add seller-payment')
                <li>
                    <a href="{{ route('admin.payment.seller-payments.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Payment
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payment.seller-payments.receive') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Receive
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payment.seller-payments.adjustment') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                    </a>
                </li>
                @endcan

                <li class="active">
                    <a href="javascript:void(0);">
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Payment Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="box-body">
                        @include('admin.payment.seller.inc.show')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
