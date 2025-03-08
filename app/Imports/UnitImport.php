<?php

namespace App\Imports;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UnitImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row) 
        {
          Unit::create([
                'name' => $row[0],
                'status' => $row[1],
                'created_by' => auth()->user()->id
            ]);
        }
    }
}
