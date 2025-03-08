<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'User',
            'code' => 'A001',
            'mobile' => '01712960833',
            'email' => 'm.sobuj.cse@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'status' => 'Active',
            'role' => "Admin",
            'created_at' => now(),
        ]);
        $user->assignRole('Super Admin');

        $admin =  User::create([
            'name' => 'Admin',
            'code' => 'A002',
            'mobile' => '01712960843',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'status' => 'Active',
            'role' => "Admin",
            'created_at' => now(),
        ]);
        $admin->assignRole('Admin');
        $seller = User::create([
            'name' => 'Seller',
            'code' => 'A003',
            'mobile' => '01712960800',
            'email' => 'seller@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'status' => 'Active',
            'role' => "Seller",
            'created_at' => now(),
        ]);
        $seller->assignRole('Seller');
        $reseller = User::create([
            'name' => 'Reseller',
            'code' => 'A004',
            'mobile' => '01712960800',
            'email' => 'reseller@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'status' => 'Active',
            'role' => "Reseller",
            'created_at' => now(),
        ]);
        $reseller->assignRole('Reseller');
        $reseller_business = User::create([
            'name' => 'Reseller Business',
            'code' => 'A005',
            'mobile' => '01712960801',
            'email' => 'resellerbusiness@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'status' => 'Active',
            'role' => "Reseller Business",
            'created_at' => now(),
        ]);
        $reseller_business->assignRole('Reseller Business');
    }
}
