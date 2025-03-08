@extends('layouts.print')
@section('content')
<div class="box-body table-responsive">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <table class="table table-bordered border-dark">
                <tr>
                    <th scope="col" style="text-align: left;font-size: 12px">Code</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->code }}</td>
                    <th scope="col" style="text-align: left;font-size: 12px">Name</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->name }}</td>
                    <th scope="col" style="text-align: left;font-size: 12px">Email</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->email }}</td>
                    <th scope="col" style="text-align: left;font-size: 12px">Number</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->mobile }}</td>
                    <th scope="col" style="text-align: left;font-size: 12px">Address</th>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $data->address }}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <table class="table table-bordered border-dark">
        <thead>
            <tr>
                <th scope="col" style="text-align: left;font-size: 12px">Sl</th>
                <th scope="col" style="text-align: left;font-size: 12px">Invoice No.</th>
                <th scope="col" style="text-align: left;font-size: 12px">Date</th>
                <th scope="col" style="text-align: left;font-size: 12px">Voucher Type</th>
                <th scope="col" style="text-align: left;font-size: 12px">Note</th>
                <th scope="col" style="text-align: left;font-size: 12px">Order Amount</th>
                <th scope="col" style="text-align: left;font-size: 12px">Sale Amount</th>
                <th scope="col" style="text-align: left;font-size: 12px">Sale Return Amount</th>
                <th scope="col" style="text-align: left;font-size: 12px">Received Amount</th>
                <th scope="col" style="text-align: left;font-size: 12px">Payment Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $orders = 0;
                $sales = 0;
                $saleReturns = 0;
                $received = 0;
                $paid = 0;
            @endphp
            @foreach ($reports as $key=>$val)
                <tr>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $key + 1 }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $val->code }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ dateFormat($val->date) }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $val->type }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">{{ $val->note }}</td>
                    <td scope="col" style="text-align: left;font-size: 12px">
                        @if ($val->type == 'Orders')
                        <?php $orders += $val->amount; ?>
                            {{ $val->amount }}
                        @endif
                    </td>
                    <td scope="col" style="text-align: left;font-size: 12px">
                        @if ($val->type == 'Sales')
                        <?php $sales += $val->amount; ?>
                            {{ $val->amount }}
                        @endif
                    </td>
                    <td scope="col" style="text-align: left;font-size: 12px">
                        @if ($val->type == 'Sale Returns')
                        <?php $saleReturns += $val->amount; ?>
                            {{ $val->amount }}
                        @endif
                    </td>
                    <td scope="col" style="text-align: left;font-size: 12px">
                        @if ($val->type == 'Received')
                            <?php $received += $val->amount; ?>
                            {{ $val->amount }}
                        @endif
                    </td>
                    <td scope="col" style="text-align: left;font-size: 12px">
                        @if ($val->type == 'Payment')
                            <?php $paid += $val->amount; ?>
                            {{ $val->amount }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th scope="col" style="text-align: left;font-size: 12px" colspan="5">Total</th>
                <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($orders, 2) }}</th>
                <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($sales, 2) }}</th>
                <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($saleReturns, 2) }}</th>
                <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($received, 2) }}</th>
                <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($paid, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection