<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_ins', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Purchase', 'Dyeing', 'Production','SaleReturn'])->default('Purchase');
            $table->unsignedBigInteger('flagable_id');
            $table->string('flagable_type');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->unsignedBigInteger('quantity');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('total_price', 14, 2);
            $table->unsignedBigInteger('used_quantity')->nullable();
            $table->decimal('cost', 14, 2)->nullable();
            $table->decimal('actual_unit_price', 14, 2)->nullable();
            $table->foreignId('fabric_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('fabric_unit_price')->nullable();
            $table->decimal('fabric_quantity')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('return_quantity')->default(0);
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
        Schema::dropIfExists('product_ins');
    }
}
