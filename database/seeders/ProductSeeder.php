<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'name' => 'Print',
            'code' => 'P00001',
            'unit_id' => 1,
            'master_type' => 'Cover',
            'product_type' => 'Fabric',
            'category_type' => 'Regular',
            'alert_quantity' => 100,
            'stock_price' => 5,
            'sale_price' => 6,
            'status' => 'Active',
            'created_by' => '1'
        ]);
        Product::create([
            'name' => 'Velvet',
            'code' => 'P00002',
            'unit_id' => 2,
            'master_type' => 'Cover',
            'product_type' => 'Fabric',
            'category_type' => 'Regular',
            'alert_quantity' => 10,
            'stock_price' => 200,
            'sale_price' => 220,
            'status' => 'Active',
            'created_by' => '1'
        ]);
        Product::create([
            'name' => 'Korean Velvet',
            'code' => 'P00003',
            'unit_id' => 2,
            'master_type' => 'Cover',
            'product_type' => 'Fabric',
            'category_type' => 'Regular',
            'alert_quantity' => 10,
            'stock_price' => 200,
            'sale_price' => 220,
            'status' => 'Active',
            'created_by' => '1'
        ]);
        Product::create([
            'name' => 'Ice Velvet',
            'code' => 'P00004',
            'unit_id' => 2,
            'master_type' => 'Cover',
            'product_type' => 'Fabric',
            'category_type' => 'Regular',
            'alert_quantity' => 10,
            'stock_price' => 200,
            'sale_price' => 220,
            'status' => 'Active',
            'created_by' => '1'
        ]);
        Product::create([
            'name' => 'Turkey',
            'code' => 'P00005',
            'unit_id' => 3,
            'master_type' => 'Cover',
            'product_type' => 'Fabric',
            'category_type' => 'Regular',
            'alert_quantity' => 10,
            'stock_price' => 600,
            'sale_price' => 700,
            'status' => 'Active',
            'created_by' => '1'
        ]);
    }
}
