<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::firstOrCreate([
            'code' => 'SUP0001',
            'name' => 'Oshni Software',
            'contact_no' => '+8801751017812',
            'contact_person' => 'Sobuj',
            'email' => 'sobuj@gmail.com',
            'opening_due' => 0,
            'address' => 'Mirpur-1',
            'status' => 'Active',
            'created_by' => '1',
        ]);
        Supplier::firstOrCreate([
            'code' => 'SUP0002',
            'name' => 'Rohim Trade',
            'contact_no' => '+8801751017812',
            'contact_person' => 'Rohim',
            'email' => 'rohim@gmail.com',
            'opening_due' => 0,
            'address' => 'Mirpur-1',
            'status' => 'Active',
            'created_by' => '1',
        ]);
    }
}
