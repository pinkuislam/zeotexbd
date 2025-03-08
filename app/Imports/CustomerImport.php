<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CustomerImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row) 
        {
            Customer::create([
                'type' => $row[0],
                'user_id' => $row[1],
                'name' => $row[2],
                'contact_name' => $row[3],
                'mobile' => $row[4],
                'email' => $row[5],
                'address' => $row[6],
                'status' => $row[7],
                'created_by' => auth()->user()->id
            ]);
        }
    }
}
