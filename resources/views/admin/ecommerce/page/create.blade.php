@extends('layouts.app')

@section('content')
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li>
                <a href="{{ route('admin.ecommerce.pages.index').qString() }}">
                    <i class="fa fa-list" aria-hidden="true"></i>Page List
                </a>
            </li>

            @can('add page')
            <li class="active">
                <a href="{{ route('admin.ecommerce.pages.create').qString() }}">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Page
                </a>
            </li>
            @endcan
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <div class="box-body">
                    <form method="POST" action="{{ route('admin.ecommerce.pages.store').qString() }}" id="are_you_sure" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        
                        @include('admin.ecommerce.page.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
