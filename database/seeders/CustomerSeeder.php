<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'name' => 'Oshni Software',
            'contact_name' => 'Customer',
            'mobile' => '01555555555',
            'email' => 'customer@gmail.com',
            'address' => 'Mirpur-1',
            'shipping_address' => 'Mirpur-1',
            'type' => 'Admin',
            'user_id' => '2',
            'shipping_rate_id' => '1',
            'status' => 'Active',
            'created_by' =>'1',
        ]);
        Customer::create([
            'name' => 'Oshni Software Marketing',
            'contact_name' => 'Sobur',
            'mobile' => '01555555555',
            'email' => 'sobur@gmail.com',
            'address' => 'Mirpur-1',
            'shipping_address' => 'Mirpur-1',
            'type' => 'Seller',
            'user_id' => '3',
            'shipping_rate_id' => '1',
            'status' => 'Active',
            'created_by' =>'1',
        ]);
    }
}
