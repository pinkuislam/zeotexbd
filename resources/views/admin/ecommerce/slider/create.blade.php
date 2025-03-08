@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.ecommerce.sliders.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i> Slider List
                </a>
            </li>

            @can('add slider')
            <li class="active">
                <a href="{{ route('admin.ecommerce.sliders.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Slider
                </a>
            </li>
            @endcan
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.ecommerce.sliders.store').qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        
                        @include('admin.ecommerce.slider.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
