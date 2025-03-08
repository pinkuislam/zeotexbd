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
            <th>Dyeing Agent</th>
            <th>:</th>
            <td>{{ $data->dyeingAgent != null ? $data->dyeingAgent->name : '' }}</td>
        </tr>
        <tr>
            <th>Type</th>
            <th>:</th>
            <td>{{ $data->type }}</td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <th>:</th>
            <td>{{ $data->total_amount }}</td>
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
    </table>
</div>