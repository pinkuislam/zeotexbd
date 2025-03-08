<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th style="width:120px;">Receipt No.</th>
            <th style="width:10px;">:</th>
            <td>{{ $data->receipt_no }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <th>:</th>
            <td>{{ dateFormat($data->date) }}</td>
        </tr>
        <tr>
            <th>Customer</th>
            <th>:</th>
            <td>{{ $data->customer != null ? $data->customer->name : '' }}</td>
        </tr>
        <tr>
            <th>Bank</th>
            <th>:</th>
            <td>
                @foreach ($data->transactions as $transaction)
                    @if ($loop->last)
                    {{ $transaction->bank->name }} - {{ $transaction->amount }}
                    @else
                    {{ $transaction->bank->name }} - {{ $transaction->amount }} ,
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th>Type</th>
            <th>:</th>
            <td>{{ $data->type }}</td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <th>:</th>
            <td>{{ $data->amount }}</td>
        </tr>
        <tr>
            <th>Note</th>
            <th>:</th>
            <td>{!! nl2br($data->note) !!}</td>
        </tr>
        <tr>
            <th>Approved By</th>
            <th>:</th>
            <td>{{ $data->approvedBy->name ?? ''}}</td>
        </tr>
        <tr>
            <th>Approved At</th>
            <th>:</th>
            <td>{{ dateFormat($data->approved_at) ?? ''}}</td>
        </tr>
        <tr>
            <th>Created By</th>
            <th>:</th>
            <td>{{ $data->createdBy->name ?? ''}}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <th>:</th>
            <td>{{ dateFormat($data->created_at) ?? ''}}</td>
        </tr>
    </table>
</div>