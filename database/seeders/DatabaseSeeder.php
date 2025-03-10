<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(UserRoleAssignSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(ShippingRateSeeder::class);
        $this->call(DeliveryAgentSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(BankSeeder::class);
    }
}
