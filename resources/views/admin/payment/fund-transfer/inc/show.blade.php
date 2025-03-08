<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th style="width:120px;">Bank (From)</th>
            <th style="width:10px;">:</th>
            <td>{{ $data->fromBank != null ? $data->fromBank->bank_name : '-' }}</td>
        </tr>
        <tr>
            <th>Bank (To)</th>
            <th>:</th>
            <td>{{ $data->toBank != null ? $data->toBank->bank_name : '-' }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <th>:</th>
            <td>{{ dateFormat($data->date) }}</td>
        </tr>
        <tr>
            <th>Note</th>
            <th>:</th>
            <td>{!! nl2br($data->note) !!}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <th>:</th>
            <td>{{ number_format($data->amount, 2) }}</td>
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
    </table>
</div>