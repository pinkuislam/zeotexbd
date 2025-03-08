<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_outs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Sale', 'Dyeing', 'PurchaseReturn', 'Damage', 'Production'])->default('Sale');
            $table->unsignedBigInteger('flagable_id');
            $table->string('flagable_type');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->unsignedBigInteger('quantity');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('net_unit_price', 14, 2);
            $table->decimal('total_price', 14, 2);
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
        Schema::dropIfExists('product_outs');
    }
}
