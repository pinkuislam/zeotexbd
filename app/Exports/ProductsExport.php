<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $products = Product::select(
            'products.code',
            'products.name',
            'products.type',
            'units.name AS unit_name',
            'products.unit_price',
            'products.alert_quantity',
            'products.status',
        )
        ->join('units', 'products.unit_id', '=', 'units.id')
        ->get();

        return $products;
    }

    public function headings(): array
    {
        return ["Code", "Name", "Type", " Unit", " Unit Price", "Alter Qty", "Status"];
    }
}
