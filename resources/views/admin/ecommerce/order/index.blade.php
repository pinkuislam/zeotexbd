@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="{{ route('admin.ecommerce.orders.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Order List
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.ecommerce.orders.index') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                            

                                <div class="form-group">
                                    <select class="form-control" name="status">
                                        <option value="">Any Status</option>
                                        @foreach (['Placed', 'In Progress', 'Ready to Ship', 'Shipped', 'Delivered', 'Returned', 'Canceled'] as $item)
                                            <option value="{{ $item }}" {{ Request::get('status') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon">From</span>
                                        <input type="text" class="form-control" id="datepickerFrom" name="from" value="{{ Request::get('from') }}">
                                        <span class="input-group-addon">To</span>
                                        <input type="text" class="form-control" id="datepickerTo" name="to" value="{{ Request::get('to') }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-custom btn-flat">Search</button>
                                    <a class="btn btn-custom btn-flat" href="{{ route('admin.ecommerce.orders.index') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>Order Id.</th>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Customer Phone</th>
                                    <th>Items</th>
                                    <th>Shipping Charge</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th class="col-action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $val)
                                    <tr>
                                        <td>{{ $val->serial_number }}</td>
                                        <td>{{ dateFormat($val->created_at) }}</td>
                                        <td>{{ $val->name ?? '-' }}</td>
                                        <td>{{ $val->phone ?? '-' }}</td>
                                        <td> 
                                            @foreach($val->ecommerceOrders as $item)
                                            {{optional($item->product)->name}} <strong class="label label-default">{{$item->quantity}}</strong> <br>
                                            @endforeach
                                        </td>
                                        <td>{{ optional($val->shipping)->rate }}</td>
                                        <td>{{ $val->total_amount }}</td>
                                        <td>{{ $val->status }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a class="btn btn-default btn-flat btn-xs dropdown-toggle"
                                                    type="button" data-toggle="dropdown">Action <span
                                                        class="caret"></span></a>
                                                <ul class="dropdown-menu dropdown-menu-right">
        
                                                    @can('show ecommerce-orders')
                                                        <li><a
                                                                href="{{ route('admin.ecommerce.orders.show', $val->id) . qString() }}"><i
                                                                    class="fa fa-eye"></i> Show</a></li>
                                                    @endcan
                                                    @can('show ecommerce-orders')
                                                        <li><a
                                                                href="{{ route('admin.ecommerce.orders.edit', $val->id) . qString() }}"><i
                                                                    class="fa fa-pencil"></i> Process To Order</a></li>
                                                    @endcan
                                                    {{-- @can('delete ecommerce-orders') 
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="deleted('{{ route('admin.ecommerce.orders.destroy', $val->id).qString() }}')">
                                                            <i class="fa fa-close"></i> Delete
                                                        </a>
                                                    </li>
                                                    @endcan --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 pagi-msg">{!! pagiMsg($records) !!}</div>
    
                        <div class="col-sm-4 text-center">
                            {{ $records->appends(Request::except('page'))->links() }}
                        </div>
    
                        <div class="col-sm-4">
                            <div class="pagi-limit-box">
                                <div class="input-group pagi-limit-box-body">
                                    <span class="input-group-addon">Show:</span>
    
                                    <select class="form-control pagi-limit" name="limit">
                                        @foreach (paginations() as $pag)
                                            <option value="{{ qUrl(['limit' => $pag]) }}"
                                                {{ $pag == Request::get('limit') ? 'selected' : '' }}>
                                                {{ $pag }}</option>
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
