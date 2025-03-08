<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $customers = Customer::select(
            'customers.type',
            'customers.user_id',
            'customers.name',
            'customers.contact_name',
            'customers.mobile',
            'customers.email',
            'customers.address',
            'customers.status'
        )
        ->get();

        return $customers;
    }

    public function headings(): array
    {
        return ["Type", "User ID", "Name", "Contact Persion", "Contact Number", "Email", "Address", "Status"];
    }
}
