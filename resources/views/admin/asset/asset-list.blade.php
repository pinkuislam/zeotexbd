@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class=active>
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Asset List Ladger
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.asset.ledger') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select name="asset_id" class="form-control">
                                        <option value="">Any Asset</option>
                                        @foreach ($assets as $asset)
                                            <option value="{{ $asset->id }}"
                                                {{ Request::get('status') == $asset->id ? 'selected' : '' }}>
                                                {{ $asset->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit"
                                        class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.asset.assets.index') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $totalquantity = 0;
                                    $totalPrice = 0;
                                ?>
                                @foreach ($reports as $val)
                                    <tr>
                                        <td>{{ $val->name }}</td>
                                        <td>{{ $val->totalQty }}</td>
                                        <td>{{ $val->totalAmount }}</td>
                                    </tr>
                                    <?php
                                        $totalquantity += $val->totalQty;
                                        $totalPrice += $val->totalAmount;
                                    ?>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right" style="font-weight: bold">Total</td>
                                    <td class="text-right" style="font-weight: bold">{{ number_format($totalquantity , 2) }}</td>
                                    <td class="text-right" style="font-weight: bold">{{ number_format($totalPrice, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
