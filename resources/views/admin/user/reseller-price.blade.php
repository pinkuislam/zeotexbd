@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.user.reseller.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Reseller List
                    </a>
                </li>

                <li class="active">
                    <a>
                        <i class="fa fa-pencil" aria-hidden="true"></i> Set Reseller Pricing
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="box-body">
                        <form method="POST" action="{{ route('admin.user.reseller.price', $data->id) }}{{ qString() }}" id="are_you_sure" class="form-horizontal">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Reseller Name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" value="{{ $data->name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Category</th>
                                                    <th>Unit</th>
                                                    <th>Sale Price</th>
                                                    <th style="width:150px;">Reseller Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($products as $val)
                                                    <tr>
                                                        <td>{{ $val->name }}</td>
                                                        <td>{{ $val->master_type == 'Cover' ? $val->category_type : $val->category->name }}</td>
                                                        <td>{{ $val->unit->name ?? '-' }}</td>
                                                        <td>{{ $val->product_type == 'Combo' ? $val->getSalePrice() : $val->sale_price }}</td>
                                                        <td>
                                                            <input type="text" class="form-control" name="products[{{ $val->id }}]" value="{{ $val->price ?? $val->unit_price }}" required @if($val->price == null)style="border-color: red;"@endif />
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group text-center">
                                        <button type="submit" class="btn btn-success btn-flat btn-lg">Submit</button>
                                        <button type="reset" class="btn btn-custom btn-flat btn-lg">Clear</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
 
</script>
@endsection
