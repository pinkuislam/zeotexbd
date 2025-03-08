@extends('snaps.layout')

@section('content')
    <section class="content fluid" style="width: 100% !important">
        <div class="box-body table-responsive p-0 m-0" style="width: 100% !important">
            <table class="table table-hover p-0 m-0" style="width: 100% !important; margin-top: 5px !important">
                <tbody>
                <tr>
                    <td style="text-align: left;font-size: 12px; font-weight: bold;"> {{$data->type}} Name: </td>
                    <td style="text-align: left;font-size: 12px">{{ $data->user->name  }}</td>
                    <td style="text-align: left;font-size: 12px; font-weight: bold;">Customer / Reseller Business Name: </td>
                    <td style="text-align: left;font-size: 12px">
                        @if($data->customer)
                            {{ $data->customer->name }}
                        @else
                            {{$data->resellerBusiness ? $data->resellerBusiness->name : '' }}
                        @endif
                    </td>
                    <td style="text-align: left;font-size: 12px; font-weight: bold;">Date: </td>
                    <td style="text-align: left;font-size: 12px">{{ dateFormat($data->date) }}</td>
                </tr>
                @php
                    $string = $data->user ? $data->user->getRoleNames()[0] == 'Super Admin' ? config('app.name', 'ZEO TEX BD')  : $data->user->name : config('app.name', 'ZEO TEX BD');
                    preg_match_all('/\b\w/', $string, $matches);
                    $firstLetters = implode('', $matches[0]);
                @endphp
                @if (count($data->images) > 0)
                    <tr>
                        <td style="text-align:center;font-size: 12px; font-weight: bold;" colspan="6">Collage</td>
                    </tr>
                    @foreach($data->images as $img)
                        <tr>
                            <td style="text-align:left; font-size: 18px; font-weight: bold;" colspan="3">Order No:: </td>
                            <td style="text-align: left; font-size: 18px; font-weight: bold;" colspan="3">{{$firstLetters}}-{{ $data->code }}</td>
                        </tr>
                        @if($loop->last)
                            <tr>
                                <td style="text-align:center;font-size: 12px;" colspan="4">  {!! MediaUploader::showImg('orders', $img->image, ['class' => 'img-responsive show_image' , 'style' => 'width:100%; height:auto;object-fit:fill;padding:5px 0']) !!}</td>
                            </tr>
                        @else
                            @if($loop->first)
                                <tr>
                                    <td style="text-align:center;font-size: 12px;" colspan="4">  {!! MediaUploader::showImg('orders', $img->image, ['class' => 'img-responsive show_image' , 'style' => 'width:100%; height:auto;object-fit:fill; margin-bottom:350px;padding:5px 0']) !!}</td>
                                </tr>
                            @else
                                <tr>
                                    <td style="text-align:center;font-size: 12px;" colspan="4">  {!! MediaUploader::showImg('orders', $img->image, ['class' => 'img-responsive show_image' , 'style' => 'width:100%; height:auto;object-fit:fill; margin-bottom:350px; margin-top:50px;padding:5px 0']) !!}</td>
                                </tr>
                            @endif
                        @endif
                    @endforeach
                @endif
                @if (count($data->items) > 0)
                    <tr>
                        <td style="text-align: left;font-size: 12px; font-weight: bold;" colspan="3"> Item Details</td>
                        <td style="text-align: center;font-size: 12px; font-weight: bold;">Quantity</td>
                    </tr>
                    @foreach($data->items as $item)
                        <tr>
                            <td style="text-align: left;font-size: 12px; font-weight: bold;" colspan="3">{{ $item->product ? $item->product->name : ''}}  {{ $item->unit ? '-'. $item->unit->name : ''}} {{ $item->color ? '-'. $item->color->name : ''}}    </td>
                            <td style="text-align: center;font-size: 12px; font-weight: bold;"> {{ number_format($item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </section>
@endsection
