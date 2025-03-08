<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>zeotexbd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>
    <section>
        <table>
            <tr>
                <td style="width: 80%;">
                    <h1 class="fw-bolder display-5" style="color: {{ $data->user ? $data->user->getRoleNames()[0] == 'Super Admin' ? '#42dada'  : $data->user->color : '#42dada' }};"> {{ $data->user ? $data->user->getRoleNames()[0] == 'Super Admin' ? config('app.name', 'Laravel')  : $data->user->name : config('app.name', 'Laravel') }}</h1>
                    <h6 class="m-0"><strong>Address: </strong>{{ $data->user->getRoleNames()[0] == 'Reseller' ? $data->user->address : '14 no, ZEO Tex bd, Pakuria Bazar, Dhaka 1230' }}.</h6>
                    <h6 class="m-0"><strong>Phone: </strong>{{ $data->user->getRoleNames()[0] == 'Reseller' ? $data->user->mobile : '+8801789-593255' }} </h6>
                </td>
                <td style="width: 20%;">
                        @php
                            $logo = 'logo.png';
                        @endphp
                        @if ($data->user )
                        @if ($data->user->image && $data->user->getRoleNames()[0] != 'Super Admin')
                          {!! MediaUploader::showImg('users', $data->user->image, ['class' => 'w-75 float-end img-fluid', 'style' => 'height:200px']) !!}
                        @else
                        <img class="w-75 float-end img-fluid" src="{{asset('assets/img/logo.png')}}" alt="{{ $data->user ? $data->user->getRoleNames()[0] == 'Super Admin' ? config('app.name', 'Laravel')  : $data->user->name : config('app.name', 'Laravel') }}" srcset="" style="height: 200px;">
                        @endif
                        @endif
                </td>
            </tr>
        </table>
    </section>
    <section class="">
        <div class="d-flex justify-content-between">
            <div>
                <h6 class="fw-bold">DATE: {{ dateFormat($data->date) }}</h6>
                <p class="m-0">Name: <span class="fw-bold">{{ $data->customer != null ? optional($data->customer)->name : optional($data->resellerBusiness)->name }}</span></p>
                <p class="m-0">Address: <span class="fw-semibold">{{ $data->customer != null ? $data->customer->address : optional($data->resellerBusiness)->address }}</span></p>
                <p class="m-0">Phone: <span class="fw-semibold">{{ $data->customer != null ? $data->customer->mobile : optional($data->resellerBusiness)->mobile }}</span></p>
            </div>
            <div>
                @php
                $string = $data->user ? $data->user->getRoleNames()[0] == 'Super Admin' ? config('app.name', 'ZEO TEX BD')  : $data->user->name : config('app.name', 'ZEO TEX BD');
                preg_match_all('/\b\w/', $string, $matches);
                $firstLetters = implode('', $matches[0]);
                @endphp
                <h6 class="fw-bold">INVOICE NO: <span class="text-danger">{{$firstLetters}}-{{$data->invoice_number}}</span></h6>
            </div>
        </div>
    </section>

    <h6 class="float-end fw-bold">Delivery: <span class="text-danger">{{ $data->delivery != null ? $data->delivery->name : '' }}</span></h6>


    <section class=" mt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="font-size: 12px;" scope="col">SI No</th>
                    <th style="font-size: 12px;" scope="col">Color</th>
                    <th style="font-size: 12px;" scope="col">Description</th>
                    <th style="font-size: 12px;" scope="col">Quantity</th>
                    <th style="font-size: 12px;" scope="col">Amount</th>
                </tr>
            </thead>
            @php
                $total = 0;
            @endphp
            <tbody>
                @if ($data->items)
                @foreach ($data->items as $key=>$val)
                  <tr>
                      <th style="font-size: 12px;" scope="row"> {{$key + 1}} </th>
                      <td style="font-size: 12px;">{{ optional($val->color)->name }}</td>
                      <td style="font-size: 12px;">{{ optional($val->product)->name }}</td>
                      <td style="font-size: 12px;">{{ number_format($val->quantity, 2) }}</td>
                      <td style="font-size: 12px;">{{ number_format($val->unit_price * $val->quantity, 2) }}</td>
                  </tr>
                  @php
                      $total += ($val->unit_price * $val->quantity) ;
                  @endphp
                @endforeach  
              @endif
           
                <tr>
                    <td style="font-size: 12px;" rowspan="6" colspan="3">
                        @if ($data->order->images)  
                            @foreach ($data->order->images as $image)
                            {!! MediaUploader::showImg('orders', $image->image, ['class' => 'w-75 float-start img-fluid', 'style' => 'height:150px; width:150px !important; padding: 5px!important']) !!}
                            @endforeach
                        @endif
                    </td>
                <tr>
                    <td style="font-size: 12px;" class="fw-semibold">Total</td>
                    <td style="font-size: 12px;">{{number_format($total, 2)}}</td>
                </tr>
              <tr>
                  <th style="font-size: 12px;" class="fw-semibold"> Shipping Charge </th>
                  <td style="font-size: 12px;">{{ number_format( $data->shipping_charge, 2) }}</td>
              </tr>
              <tr>
                  <th style="font-size: 12px;" class="fw-semibold"> Discount </th>
                  <td style="font-size: 12px;">{{ number_format( $data->discount_amount, 2) }}</td>
              </tr>
              <tr>
                  <th style="font-size: 12px;" class="fw-semibold"> Advance </th>
                  <td style="font-size: 12px;">{{ number_format( $data->order->advance_amount, 2) }}</td>
              </tr>
                <tr>
                    <th style="font-size: 12px;">Payable Amount</th>
                    <td style="font-size: 12px;">{{ number_format( ($total - $data->order->advance_amount - $data->discount_amount + $data->shipping_charge), 2)}}</td>
                </tr>
                </tr>
            </tbody>
        </table>
    </section>
    <section class="mt-4">
        <div class=" fw-semibold">
            <li>Thanks For Stay With Us.</li>
            <li>Please follow on our facebook page.</li>
            <li>Hotline Number: <span class="fs-5 text-success fw-semibold"> {{ $data->user ? $data->user->mobile : '+8801789-593255' }}</span></li>
            <p class="mt-3">____________________________</p>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
        <script>
            window.print();
        </script>
</body>

</html>