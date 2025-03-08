<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoryStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessory_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('note')->nullable();
            $table->date('date');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('challan_number')->nullable();
            $table->string('challan_image')->nullable();
            $table->unsignedBigInteger('total_quantity')->default(0);
            $table->decimal('subtotal_amount', 14, 2)->default(0);
            $table->decimal('cost', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
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
        Schema::dropIfExists('accessory_stocks');
    }
}
