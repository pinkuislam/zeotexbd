<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Received', 'Payment', 'Adjustment']);
            $table->enum('flag', ['Income', 'Expense', 'Supplier', 'Customer', 'Seller', 'Reseller','Reseller Business', 'Delivery', 'Transfer','Loan', 'Dyeing']);
            $table->unsignedBigInteger('flagable_id');
            $table->string('flagable_type');
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->dateTime('datetime');
            $table->text('note')->nullable();
            $table->decimal('amount', 14, 2);
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
