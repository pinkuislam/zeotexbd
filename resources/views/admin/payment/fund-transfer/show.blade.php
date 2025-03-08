@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.payment.fund-transfers.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Fund Transfer List
                    </a>
                </li>

                @can('add fund-transfer')
                <li>
                    <a href="{{ route('admin.payment.fund-transfers.create') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Fund Transfer
                    </a>
                </li>
                @endcan

                <li class="active">
                    <a href="javascript:void(0);">
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Fund Transfer Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="box-body">
                        @include('admin.payment.fund-transfer.inc.show')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
