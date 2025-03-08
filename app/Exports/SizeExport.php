<?php

namespace App\Exports;

use App\Models\Size;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SizeExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $sizes = Size::select(
            'sizes.name',
            'sizes.status'
        )
        ->get();

        return $sizes;
    }

    public function headings(): array
    {
        return ["Name","Status"];
    }
}
