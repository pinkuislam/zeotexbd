<?php

namespace App\Exports;

use App\Models\ShippingRate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShippingRateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $shipping_rates = ShippingRate::select(
            'shipping_rates.name',
            'shipping_rates.area',
            'shipping_rates.rate',
            'shipping_rates.status'
        )
        ->get();

        return $shipping_rates;
    }

    public function headings(): array
    {
        return ["Name", "Area", "Rate", "Status"];
    }
}
