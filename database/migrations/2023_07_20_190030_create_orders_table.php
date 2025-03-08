<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('date');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('reseller_business_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('shipping_rate_id')->nullable()->constrained('shipping_rates')->nullOnDelete();
            $table->foreignId('delivery_agent_id')->nullable()->constrained('shipping_rates')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['Admin', 'Seller','Reseller','Reseller Business'])->default('Admin');
            $table->text('note')->nullable();
            $table->decimal('shipping_charge', 14, 2)->nullable()->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('advance_amount', 14, 2)->nullable()->default(0);
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->decimal('amount', 14, 2)->default(0);
            $table->enum('has_stock_done', ['Turkey', 'Yes', 'No'])->nullable()->default('No');
            $table->enum('status', ['Ordered', 'Pending','Processing','Shipped','Delivered','Canceled'])->nullable()->default('Ordered');
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
        Schema::dropIfExists('orders');
    }
}
