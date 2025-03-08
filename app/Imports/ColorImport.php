<?php

namespace App\Imports;
use App\Models\Color;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ColorImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row) 
        {
          Color::create([
                'name' => $row[0],
                'color_code' => $row[1],
                'status' => $row[2],
                'created_by' => auth()->user()->id
            ]);
        }
    }
}
