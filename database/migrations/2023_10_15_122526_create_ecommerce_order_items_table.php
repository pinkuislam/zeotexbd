<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecommerce_order_id')->constrained('ecommerce_orders')->cascadeOnDelete();
            $table->string('barcode');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();
            $table->decimal('old_price')->default(0);
            $table->decimal('sale_price')->default(0);
            $table->decimal('amount')->default(0);
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
        Schema::dropIfExists('ecommerce_order_items');
    }
}
