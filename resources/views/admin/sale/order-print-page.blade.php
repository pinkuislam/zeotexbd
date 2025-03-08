@extends('layouts.app')
@push('styles')
    <style>
        tr td {
            padding: 0 !important;
            margin: 0 !important;
        }
    </style>
@endpush

@section('content')
    <section class="content fluid" style="width: 100% !important">
        <div class="box-body table-responsive p-0 m-0" style="width: 100% !important">
            @foreach ($result as $key => $val)
            <table class="table table-hover p-0 m-0" style="width: 100% !important; margin-top: 5px !important">
                <tbody>
                        <tr>
                            <td style="text-align: left;font-size: 12px; font-weight: bold;"> {{$val->type}} Name: </td>
                            <td style="text-align: left;font-size: 12px">{{ $val->user->name  }}</td>
                            <td style="text-align: left;font-size: 12px; font-weight: bold;">Customer / Reseller Business Name: </td>
                            <td style="text-align: left;font-size: 12px">  
                                @if($val->customer)
                                {{ $val->customer->name }}
                                @else
                                {{$val->resellerBusiness ? $val->resellerBusiness->name : '' }}
                                @endif
                            </td>
                            <td style="text-align: left;font-size: 12px; font-weight: bold;">Date: </td>
                            <td style="text-align: left;font-size: 12px">{{ dateFormat($val->date) }}</td>
                        </tr>
                        @php
                        $string = $val->user ? $val->user->getRoleNames()[0] == 'Super Admin' ? config('app.name', 'ZEO TEX BD')  : $val->user->name : config('app.name', 'ZEO TEX BD');
                        preg_match_all('/\b\w/', $string, $matches);
                        $firstLetters = implode('', $matches[0]);
                        @endphp
                        @if (count($val->images) > 0)
                            <tr>
                                <td style="text-align:center;font-size: 12px; font-weight: bold;" colspan="6">Collage</td>
                            </tr>
                            @foreach($val->images as $img)
                            <tr>
                                <td style="text-align:left; font-size: 18px; font-weight: bold;" colspan="3">Order No:: </td>
                                <td style="text-align: left; font-size: 18px; font-weight: bold;" colspan="3">{{$firstLetters}}-{{ $val->code }}</td>
                            </tr>
                            @if($loop->last) 
                            <tr>
                                <td style="text-align:center;font-size: 12px;" colspan="4">  {!! MediaUploader::showImg('orders', $img->image, ['class' => 'img-responsive show_image' , 'style' => 'width:100%; height:auto;object-fit:fill;padding:5px 0']) !!}</td>
                            </tr>
                            @else 
                            @if($loop->first) 
                            <tr>
                                <td style="text-align:center;font-size: 12px;" colspan="4">  {!! MediaUploader::showImg('orders', $img->image, ['class' => 'img-responsive show_image' , 'style' => 'width:100%; height:auto;object-fit:fill; margin-bottom:320px; padding:5px 0']) !!}</td>
                            </tr>
                            @else 
                            <tr>
                                <td style="text-align:center;font-size: 12px;" colspan="4">  {!! MediaUploader::showImg('orders', $img->image, ['class' => 'img-responsive show_image' , 'style' => 'width:100%; height:auto;object-fit:fill; margin-bottom:350px; margin-top:50px;padding:5px 0']) !!}</td>
                            </tr>
                             @endif
                             @endif
                            @endforeach
                        @endif
                        @if (count($val->items) > 0)
                        <tr>
                            <td style="text-align: left;font-size: 12px; font-weight: bold;" colspan="3"> Item Details</td>
                            <td style="text-align: center;font-size: 12px; font-weight: bold;">Quantity</td>
                        </tr>
                        @foreach($val->items as $item)
                                <tr>
                                    <td style="text-align: left;font-size: 12px; font-weight: bold;" colspan="3">{{ $item->product ? $item->product->name : ''}}  {{ $item->unit ? '-'. $item->unit->name : ''}} {{ $item->color ? '-'. $item->color->name : ''}}    </td>
                                    <td style="text-align: center;font-size: 12px; font-weight: bold;"> {{ number_format($item->quantity, 2) }}</td>
                                </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
                @endforeach
        </div>
    </section>
    @endsection
<script>
    window.print();
</script>
