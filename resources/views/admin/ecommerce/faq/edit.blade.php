@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.ecommerce.faq.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Faq List
                </a>
            </li>

            @can('add faq')
            <li>
                <a href="{{ route('admin.ecommerce.faq.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Faq
                </a>
            </li>
            @endcan

            <li class="active">
                <a href="javascript:void(0);">
                    <i class="fa fa-edit" aria-hidden="true"></i> Edit Faq
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.ecommerce.faq.update', $data->id).qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('admin.ecommerce.faq.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
