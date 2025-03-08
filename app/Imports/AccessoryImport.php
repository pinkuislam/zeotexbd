<?php

namespace App\Imports;
use App\Models\Accessory;
use App\Models\Unit;
use App\Services\CodeService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccessoryImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row) 
        {
            $code = CodeService::generate(Accessory::class, 'A', 'code');
            $unit = Unit::where('name', 'LIKE', '%'. $row[1].'%')->first();
            Accessory::create([
                'name' => $row[0],
                'code' => $code,
                'unit_id' => $unit->id,
                'unit_price' => $row[2],
                'alert_quantity' => $row[3],
                'status' => $row[4],
                'created_by' => auth()->user()->id
            ]);
        }
    }
}