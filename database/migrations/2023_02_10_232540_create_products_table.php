<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('master_type', masterTypes())->default('Cover');
            $table->enum('category_type', categoryTypes())->nullable();
            $table->enum('product_type', productTypes())->nullable();
            $table->foreignId('category_id')->nullable()->comment('for other')->constrained('categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('alert_quantity', 12, 2)->nullable()->default(0);
            $table->enum('seat_count', [0,1,2,3,4])->default(0);
            $table->decimal('stock_price', 14, 2)->default(0);
            $table->decimal('sale_price', 14, 2)->default(0);
            $table->decimal('reseller_price', 14, 2)->default(0);
            $table->enum('status', ['Active', 'Deactivated'])->default('Active');
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
        Schema::dropIfExists('products');
    }
}
