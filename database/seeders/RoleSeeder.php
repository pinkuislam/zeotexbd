<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);
        Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);
        Role::firstOrCreate([
            'name' => 'Seller',
            'guard_name' => 'web'
        ]);
        Role::firstOrCreate([
            'name' => 'Reseller',
            'guard_name' => 'web'
        ]);
        Role::firstOrCreate([
            'name' => 'Reseller Business',
            'guard_name' => 'web'
        ]);
    }
}