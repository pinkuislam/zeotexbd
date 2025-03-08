@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li>
                    <a href="{{ route('admin.report.customer') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Customer History
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $customer->name }}</td>
                                        <th>Email</th>
                                        <td>{{ $customer->email }}</td>
                                        <th>Phone Number</th>
                                        <td>{{ $customer->mobile }}</td>
                                        <th>Address</th>
                                        <td>{{ $customer->address }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Order Date</th>
                                    <th>Order No</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer->orders as $key=>$order)
                                    <tr>
                                        @if($order)
                                        <td>{{$key + 1}}</td>
                                        <td>{{$order->date}}</td>
                                        <td><a href="{{ route('admin.orders.show', $order->id) . qString() }}">{{$order->code}}</a></td>
                                        <td>{{$order->amount}}</td>
                                        <td>{{$order->status}}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
