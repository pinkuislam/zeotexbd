<?php

namespace App\Imports;

use App\Models\ShippingRate;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ShippingRateImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row) 
        {
            ShippingRate::create([
                'name' => $row[0],
                'area' => $row[1],
                'rate' => $row[2],
                'status' => $row[3],
                'created_by' => auth()->user()->id
            ]);
        }
    }
}
