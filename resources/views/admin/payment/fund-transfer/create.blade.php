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
            <li class="active">
                <a href="{{ route('admin.payment.fund-transfers.create') . qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Fund Transfer
                </a>
            </li>
            @endcan
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.payment.fund-transfers.store').qString() }}" id="are_you_sure" class="form-horizontal">
                        @csrf
                        
                        @include('admin.payment.fund-transfer.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
