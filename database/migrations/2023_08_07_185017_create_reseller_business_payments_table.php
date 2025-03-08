<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerBusinessPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reseller_business_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_business_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete()->comment('If came from sale');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete()->comment('If came from order');
            $table->enum('type', ['Received', 'Adjustment', 'Payment'])->default('Received');
            $table->date('date');
            $table->string('receipt_no');
            $table->decimal('amount', 14, 2);
            $table->text('note')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('reseller_business_payments');
    }
}
