<?php

namespace Database\Seeders;

use App\Models\ShippingRate;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ShippingRate::firstOrCreate([
            'name' => 'Flat Rate',
            'area' => 'Bangladesh',
            'rate' => '120',
            'note' => 'Test',
            'status' => 'Active'
        ]);
        ShippingRate::firstOrCreate([
            'name' => 'Inside Dhaka',
            'area' => 'Dhaka',
            'rate' => '60',
            'note' => 'Test',
            'status' => 'Active'
        ]);
        ShippingRate::firstOrCreate([
            'name' => 'Outside Dhaka',
            'area' => 'Bangladesh',
            'rate' => '150',
            'note' => 'Test',
            'status' => 'Active'
        ]);
        ShippingRate::firstOrCreate([
            'name' => 'Free',
            'area' => 'Bangladesh',
            'rate' => '0',
            'note' => 'Test',
            'status' => 'Active'
        ]);   
    }
}
