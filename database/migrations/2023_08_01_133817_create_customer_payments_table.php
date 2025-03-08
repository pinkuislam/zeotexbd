<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete()->comment('If came from sale');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete()->comment('If came from order');
            $table->enum('type', ['Received', 'Adjustment', 'Payment'])->default('Received');
            $table->enum('is_advance', ['Yes', 'No'])->default('No');
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
        Schema::dropIfExists('customer_payments');
    }
}
