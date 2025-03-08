<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('date');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('order_code')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('reseller_business_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['Admin', 'Seller','Reseller'])->default('Admin');
            $table->foreignId('shipping_rate_id')->nullable()->constrained('shipping_rates')->nullOnDelete();
            $table->foreignId('delivery_agent_id')->nullable()->constrained('shipping_rates')->nullOnDelete();
            $table->string('invoice_number')->nullable();
            $table->text('note')->nullable();
            $table->decimal('deduction_amount', 14, 2)->default(0);
            $table->decimal('vat_percent', 4, 2)->default(0);
            $table->decimal('vat_amount', 13, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('advance_amount', 14, 2)->default(0);
            $table->decimal('shipping_charge', 12, 2)->default(0);
            $table->decimal('extra_shipping_charge', 12, 2)->default(0);
            $table->decimal('subtotal_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('commission_percent', 14, 2)->default(0);
            $table->decimal('commission_amount', 14, 2)->default(0);
            $table->decimal('reseller_amount', 14, 2)->default(0);
            $table->enum('status', ['Pending','Processing','Shipped','Delivered','Canceled'])->default('Pending');
            $table->enum('has_return', ['Yes','No'])->default('No');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
