<?php

namespace App\Exports;

use App\Models\Accessory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccessoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $accessories = Accessory::select(
            'accessories.code',
            'accessories.name',
            'accessories.unit_id',
            'accessories.unit_price',
            'accessories.alert_quantity',
            'accessories.status'
        )
        ->get();

        return $accessories;
    }

    public function headings(): array
    {
        return ["Code","Name" ,"Unit","Unit Price","Alert Quantity","Status"];
    }
}