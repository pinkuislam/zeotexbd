<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_orders', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number');
            $table->string('name');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->decimal('shipping_rate');
            $table->foreignId('shipping_rate_id')->constrained('shipping_rates')->cascadeOnDelete();
            $table->unsignedBigInteger('total_quantity');
            $table->decimal('sub_total_amount');
            $table->decimal('total_amount');
            $table->enum('status', ['Placed', 'Processing', 'Shipped', 'Delivered', 'Returned', 'Canceled'])->default('Placed');
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
        Schema::dropIfExists('ecommerce_orders');
    }
}
