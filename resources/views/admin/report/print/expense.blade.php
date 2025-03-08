@extends('layouts.print')
@section('content')
<table class="table table-bordered border-dark">
    <thead>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px">No.</th>
            <th scope="col" style="text-align: left;font-size: 12px">Date</th>
            <th scope="col" style="text-align: left;font-size: 12px">Bank</th>
            <th scope="col" style="text-align: left;font-size: 12px">Note</th>
            <th scope="col" style="text-align: left;font-size: 12px">Category</th>
            <th scope="col" style="text-align: left;font-size: 12px">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $val)
            <tr>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->expense_number }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->date }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->bank_name . ' - ' . $val->account_no }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->note }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ $val->incomeExpenseName }}</td>
                <td scope="col" style="text-align: left;font-size: 12px">{{ number_format($val->amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th scope="col" style="text-align: left;font-size: 12px" colspan="5">Total</th>
            <th scope="col" style="text-align: left;font-size: 12px">{{ number_format($reports->sum('amount'), 2) }}</th>
        </tr>
    </tfoot>
</table>

@endsection