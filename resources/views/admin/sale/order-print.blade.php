@extends('layouts.app')
@push('styles')
<style>
    .show_image{
        height: 80px !important;
        width: 80px !important;
    }
</style>    
@endpush
@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{ isset($list) ? 'class=active' : '' }}>
                    <a href="{{ route('admin.orders.print') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Order List Print
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                    <div class="tab-pane active">
                        <form method="GET" action="{{ route('admin.orders.print') }}" class="form-inline">
                            <div class="box-header text-right">
                                <div class="row">
                                    <div class="form-group">
                                        <select class="form-control select2" name="customer_id">
                                            <option value="">Any Customer</option>
                                            @foreach ($customers as $val)
                                                <option value="{{ $val->id }}"
                                                    {{ Request::get('customer_id') == $val->id ? 'selected' : '' }}>
                                                    {{ $val->name .'-'. $val->mobile}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control " name="status">
                                            <option value="">Any Status</option>
                                            @foreach (['Ordered','Processing','Delivered'] as $status)
                                                <option value="{{ $status }}"
                                                    {{ Request::get('status') == $status ? 'selected' : '' }}>
                                                    {{ $status }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control " name="type">
                                            <option value="">Any Type</option>
                                            @foreach (['Seller','Reseller'] as $type)
                                                <option value="{{ $type }}"
                                                    {{ Request::get('type') == $type ? 'selected' : '' }}>
                                                    {{ $type }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="from" id="datepickerFrom"
                                            value="{{ dbDateRetrieve(Request::get('from')) }}" placeholder="From Date">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="to" id="datepickerTo"
                                            value="{{ dbDateRetrieve(Request::get('to')) }}" placeholder="To Date">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="q" value="" placeholder="Write your search text...">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit"  name="action" value="print" class="btn btn-info btn-flat">Print</button>
                                        <button type="submit"  name="action" value="search" class="btn btn-custom btn-flat">Search</button>
                                        <a class="btn btn-warning btn-flat" href="{{ route('admin.orders.print') }}">X</a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Date</th>
                                        <th>Admin/Seller/Reseller</th>
                                        <th>Customer/Reseller Business</th>
                                        <th>Address</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result as $val)
                                        <tr>
                                            <td>{{ $val->code }}</td>
                                            <td>{{ dateFormat($val->date) }}</td>
                                            <td>{{ $val->user->name }}</td>
                                            <td>
                                                @if($val->customer)
                                                    {{ $val->customer->name }} -  {{ $val->customer->mobile }}
                                                @else
                                                    {{$val->resellerBusiness ? $val->resellerBusiness->name . '-'. $val->resellerBusiness->mobile : '' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($val->customer)
                                                    {{ $val->customer->address }}
                                                @else
                                                    {{$val->resellerBusiness ? $val->resellerBusiness->address : '' }}
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($val->items as $key => $item)
                                                    {{ $item->product->name ?? '-' }}
                                                    <span
                                                        class="label label-default">{{ number_format($item->quantity, 0) }} {{ $item->unit ? $item->unit->name: '' }} {{ $item->color ? $item->color->name: '' }}</span>
                                                    @if (($key + 1) % 3 == 0)
                                                    <br>
                                                    @else
                                                    @if ($loop->last)
                                                    @else
                                                    ,
                                                    @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ number_format($val->amount, 2) }}
                                            </td>
                                            <td>{{ isset($val->createdBy) ? $val->createdBy->name : '' }}</td>
                                            <td>
                                               {{ $val->status }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-4 pagi-msg">{!! pagiMsg($result) !!}</div>

                            <div class="col-sm-4 text-center">
                                {{ $result->appends(Request::except('page'))->links() }}
                            </div>

                            <div class="col-sm-4">
                                <div class="pagi-limit-box">
                                    <div class="input-group pagi-limit-box-body">
                                        <span class="input-group-addon">Show:</span>
                                        <select class="form-control pagi-limit" name="limit">
                                            @foreach (paginations() as $pag)
                                                <option value="{{ qUrl(['limit' => $pag]) }}" {{ $pag == Request::get('limit') ? 'selected' : '' }}>{{ $pag }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </section>

@endsection