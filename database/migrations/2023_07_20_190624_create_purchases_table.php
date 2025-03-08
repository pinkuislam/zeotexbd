<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('date');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('challan_number')->nullable();
            $table->string('challan_image')->nullable();
            $table->enum('type', ['Finished', 'Raw', 'Turkey'])->default('Finished');
            $table->text('note')->nullable();
            $table->decimal('vat_percent', 4, 2)->default(0);
            $table->decimal('vat_amount', 13, 2)->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->decimal('adjust_amount', 14, 2)->default(0);
            $table->decimal('subtotal_amount', 14, 2)->default(0);
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
        Schema::dropIfExists('purchases');
    }
}
