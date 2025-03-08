<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Color::firstOrCreate([
            'name' => 'Red',
            'status' => 'Active'
        ]);
        Color::firstOrCreate([
            'name' => 'Green',
            'status' => 'Active'
        ]);
        Color::firstOrCreate([
            'name' => 'Yellow',
            'status' => 'Active'
        ]);
        Color::firstOrCreate([
            'name' => 'Blue',
            'status' => 'Active'
        ]);
        Color::firstOrCreate([
            'name' => 'Black',
            'status' => 'Active'
        ]);
    }
}
