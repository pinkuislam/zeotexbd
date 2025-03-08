<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $assets = Asset::select(
            'assets.name',
            'assets.status'
        )
        ->get();

        return $assets;
    }

    public function headings(): array
    {
        return ["Name","Status"];
    }
}