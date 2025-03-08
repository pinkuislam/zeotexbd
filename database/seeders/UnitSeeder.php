<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::firstOrCreate([
            'name' => 'gm',
            'status' => 'Active'
        ]);
        Unit::firstOrCreate([
            'name' => 'goj',
            'status' => 'Active'
        ]);
        Unit::firstOrCreate([
            'name' => 'pcs',
            'status' => 'Active'
        ]);
    }
}
