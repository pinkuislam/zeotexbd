<?php

namespace App\Exports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnitExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $units = Unit::select(
            'units.name',
            'units.status'
        )
        ->get();

        return $units;
    }

    public function headings(): array
    {
        return ["Name","Status"];
    }
}
