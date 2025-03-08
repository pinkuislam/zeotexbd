<?php

namespace App\Exports;

use App\Models\Color;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ColorExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $colors = Color::select(
            'colors.name',
            'colors.color_code',
            'colors.status'
        )
        ->get();

        return $colors;
    }

    public function headings(): array
    {
        return ["Name","Color Code","Status"];
    }
}
