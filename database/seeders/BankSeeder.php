<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       Bank::create([
        'code' => 'B00001',
        'account_name' => 'Cash',
        'account_no' => '11111111111',
        'bank_name' => 'Cash',
        'branch_name' => 'My Office',
        'opening_balance' => 0,
        'status' => 'Active',
        'created_by' => 1
       ]);
       Bank::create([
        'code' => 'B00002',
        'account_name' => 'Test',
        'account_no' => '11111111111',
        'bank_name' => 'DBBL',
        'branch_name' => 'Mirpur-1',
        'opening_balance' => 0,
        'status' => 'Active',
        'created_by' => 1
       ]);
    }
}
