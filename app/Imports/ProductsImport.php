<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Services\CodeService;

class ProductsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row) 
        {
            $unit = Unit::firstOrCreate([
                'name' => $row[2],
                'status' => 'Active',
            ]);
            $code = CodeService::generate(Product::class, 'P', 'code');

            $data = Product::create([
                'code' => $code,
                'name' => $row[0],
                'type' => $row[1],
                'unit_id' => $unit->id,
                'unit_price' => $row[3],
                'alert_quantity' => $row[4],
                'status' => $row[5],
                'created_by' => auth()->user()->id
            ]);
        }
    }
}
