<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $arrPermissions = [
            // Role
            'role' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],

            // Users
            'admin' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'seller' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'reseller' => [
                'list',
                'show',
                'add',
                'edit',
                'price_setup',
                'delete'
            ],
            'reseller_business' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'staff' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'supplier' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'dyeing-agent' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'customer' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'delivery_agent' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'investor' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'loan-holder' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            // Units
            'unit' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            // Sizes
            'size' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            // Colors
            'color' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            // Categories
            'category' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            // Products
            'product' => [
                'list',
                'show',
                'add',
                'edit',
                'status',
                'ecommerce_setup'
            ],
            // Shipping Method
            'shipping_method' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            // Bank
            'bank' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            // Income Expense Category
            'income_category' => [
                'list',
                'show',
                'add',
                'edit'
            ],
            'expense_category' => [
                'list',
                'show',
                'add',
                'edit'
            ],
            // Assets
            'asset' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            'asset-item' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            // Accessory
            'accessory' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            'accessory-purchase' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            'accessory-consume' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            'accessory-purchase_return' => [
                'list',
                'show',
                'add',
                'edit',
                'status'
            ],
            // Sale Order
            'orders' => [
                'list',
                'show',
                'add',
                'edit',
                'print',
                'delete'
            ],
            // Purchase
            'purchase' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            // Purchase Return
            'purchase-return' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            // Production
            'production' => [
                'list',
                'show',
                'add',
                'delete'
            ],

            // Send to Dyeing
            'send-dyeing' => [
                'list',
                'show',
                'add',
                'delete'
            ],

            // Send to Dyeing
            'receive-dyeing' => [
                'list',
                'show',
                'add',
                'delete'
            ],
            // Damage
            'damage' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            // Sale
            'sale' => [
                'list',
                'show',
                'add',
                'edit',
                'invoice',
                'delete'
            ],
            // Sale Return
            'sale-return' => [
                'list',
                'show',
                'add',
                'delete'
            ],
            // Payment
            'income' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
            ],
            'expense' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
            ],
            'loan' => [
                'list',
                'show',
                'add',
                'edit',
                'delete'
            ],
            'invest' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
            ],
            'customer-payment' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
                'approval'
            ],
            'supplier-payment' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
                'approval'
            ],
            'dyeing-agent-payment' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
                'approval'
            ],
            'fund-transfer' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
                'approval'
            ],
            'reseller-business-payment' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
                'approval'
            ],
            'reseller-payment' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
                'approval'
            ],
            // 'seller-payment' => [
            //     'list',
            //     'add',
            //     'show',
            //     'edit',
            //     'delete',
            //     'approval'
            // ],
            'delivery-agent-payment' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
                'approval'
            ],
            'slider' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
            ],
            'page' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
            ],
            'highlight' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
            ],
            'faq' => [
                'list',
                'add',
                'show',
                'edit',
                'delete',
            ],
            'ecommerce-orders' => [
                'list',
                'show',
                'status',
                'delete',
            ],
            'site-setting' => [
                'add',
            ],
            // Ledger
            'ledger' => [
                'raw-material',
                'finished',
                'customer',
                'supplier',
                'seller',
                'reseller',
                'reseller business',
                'delivery agent',
                'dyeing-agent',
                'expense',
                'income',
                'bank',
                'income statement',
                'balance sheet',
                'asset',
                'orders',
                'accessory',
            ],
        ];


        if (!empty($arrPermissions)) {
            foreach ($arrPermissions as $key => $apArr) {
                foreach ($apArr as $ap) {
                    Permission::updateOrCreate(
                        ['module_name' => $key, 'name' => $ap . ' ' . $key, 'guard_name' => 'web']
                    );
                }
            }
        }
    }
}
