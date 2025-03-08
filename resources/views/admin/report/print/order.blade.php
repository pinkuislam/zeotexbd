@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">Code</th>
            <th scope="col" style="text-align: left;font-size: 12px">Admin/Seller/Reseller</th>
            <th scope="col" style="text-align: left;font-size: 12px">Customer/Reseller Business</th>
            <th scope="col" style="text-align: left;font-size: 12px">Delivery Agent</th>
            <th scope="col" style="text-align: left;font-size: 12px">Date</th>
            <th scope="col" style="text-align: left;font-size: 12px">Collage</th>
            <th scope="col" style="text-align: left;font-size: 12px">Items</th>
            <th scope="col" style="text-align: left;font-size: 12px">Created By</th>
            <th scope="col" style="text-align: left;font-size: 12px">Status</th>
            <th scope="col" style="text-align: left;font-size: 12px">Discount Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Shipping Charage</th>
            <th scope="col" style="text-align: left;font-size: 12px">Total Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Advance Amount</th>
            <th scope="col" style="text-align: left;font-size: 12px">Due Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($result as $val)
        <tr>
            <td scope="col" style="text-align: left;font-size: 12px">{{ $val->code }}</td>
            <td scope="col" style="text-align: left;font-size: 12px">{{ optional($val->user)->name }}</td>
            <td scope="col" style="text-align: left;font-size: 12px">
                @if($val->customer)
                {{ $val->customer->name }} - {{ $val->customer->mobile }}
                @else
                {{$val->resellerBusiness ? $val->resellerBusiness->name . '-'. $val->resellerBusiness->mobile  : '' }}
                @endif
            </td>
            <td scope="col" style="text-align: left;font-size: 12px">{{ optional($val->delivery)->name }}</td>
            <td scope="col" style="text-align: left;font-size: 12px">{{ dateFormat($val->date) }}</td>
            <td scope="col" style="text-align: left;font-size: 12px">
                @if ($val->images)
                    @foreach ($val->images as $item)
                    {!! MediaUploader::showImg('orders', $item->image, ['class' => 'img-responsive show_image' , 'style' => 'width:50px;']) !!}
                    @endforeach
                @endif
            </td>
            <td scope="col" style="text-align: left;font-size: 12px">
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
            <td scope="col" style="text-align: left;font-size: 12px">{{ isset($val->createdBy) ? $val->createdBy->name : '' }}</td>
            <td scope="col" style="text-align: left;font-size: 12px">
               {{ $val->status }}
            </td>
            <td scope="col" style="text-align: left;font-size: 12px">
                {{ number_format($val->discount_amount, 2) }}
            </td>
            <td scope="col" style="text-align: left;font-size: 12px">
                {{ number_format($val->shipping_charge, 2) }}
            </td>
            <td scope="col" style="text-align: left;font-size: 12px">
                {{ number_format($val->amount, 2) }}
            </td>
            <td scope="col" style="text-align: left;font-size: 12px">
                {{ number_format($val->advance_amount, 2) }}
            </td>
            <td scope="col" style="text-align: left;font-size: 12px">
                {{ number_format( ($val->amount - $val->advance_amount), 2) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection