<?php

namespace App\Imports;
use App\Models\Size;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SizeImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row) 
        {
          Size::create([
                'name' => $row[0],
                'status' => $row[1],
                'created_by' => auth()->user()->id
            ]);
        }
    }
}
