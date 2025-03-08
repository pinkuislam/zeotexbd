@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class=active>
                    <a>
                        <i class="fa fa-list" aria-hidden="true"></i> Accessory Ladger
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.accessory.ledger.details') . '?accessory_id=' . Request::get('accessory_id') }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Accessory Reports Details
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <form method="GET" action="{{ route('admin.accessory.ledger') }}" class="form-inline">
                        <div class="box-header text-right">
                            <div class="row">
                                <div class="form-group">
                                    <select name="accessory_id" class="form-control">
                                        <option value="">Any Accessory</option>
                                        @foreach ($accessories as $accessory)
                                            <option value="{{ $accessory->id }}"
                                                {{ Request::get('accessory_id') == $accessory->id ? 'selected' : '' }}>
                                                {{ $accessory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="q"
                                        value="{{ Request::get('q') }}" placeholder="Write your search text...">
                                </div>

                                <div class="form-group">
                                    <button type="submit"  name="action" value="print" class="btn btn-custom btn-flat">Print</button>
                                    <button type="submit"  name="action" value="search" class="btn btn-info btn-flat">Search</button>
                                    <a class="btn btn-warning btn-flat" href="{{ route('admin.accessory.ledger') }}">X</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Purchase Quantity</th>
                                    <th>Used Quantity</th>
                                    <th>Stock Quantity</th>
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
                                        <td>
                                            <a href="{{ route('admin.accessory.ledger.details') . '?accessory_id='. $val->id }}">{{ $val->code }}</a>
                                        </td>
                                        <td>{{ $val->name }}</td>
                                        <td>{{ $val->totalQty }}</td>
                                        <td>{{ $val->totalUsedQty }}</td>
                                        <td>{{ $val->stockQty }}</td>
                                        <td>{{ ($val->stockQty * $val->unit_price) }}</td>
                                    </tr>
                                    <?php
                                        $totalquantity += $val->stockQty;
                                        $totalPrice += ($val->stockQty * $val->unit_price);
                                    ?>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right" style="font-weight: bold" colspan="4">Total</td>
                                    <td class="text-right" style="font-weight: bold">{{ $totalquantity }}</td>
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
