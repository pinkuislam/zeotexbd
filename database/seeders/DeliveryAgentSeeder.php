<?php

namespace Database\Seeders;

use App\Models\DeliveryAgent;
use App\Services\CodeService;
use Illuminate\Database\Seeder;

class DeliveryAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DeliveryAgent::firstOrCreate([
            'code' => 'DA00001',
            'name' => 'DHL',
            'mobile' => '01555555555',
            'emergency_mobile' => '01555555555',
            'type' => 'Agent',
            'status' => 'Active'
        ]);
        DeliveryAgent::firstOrCreate([
            'code' => 'DA00002',
            'name' => 'Staff',
            'mobile' => '01555555556',
            'emergency_mobile' => '01555555556',
            'type' => 'Staff',
            'status' => 'Active'
        ]);
    }
}
