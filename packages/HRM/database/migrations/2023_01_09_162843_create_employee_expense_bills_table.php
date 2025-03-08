<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_expense_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date');
            $table->text('daily_summary')->nullable();
            $table->unsignedDecimal('da_amount', 10, 2);
            $table->unsignedDecimal('ta_amount', 10, 2);
            $table->unsignedDecimal('hotel_bill', 10, 2)->default(0);
            $table->unsignedDecimal('total_amount', 10, 2);
            $table->enum('is_holiday', ['Yes', 'No'])->default('No');
            $table->enum('status', ['Pending', 'Approved', 'Canceled', 'Processed'])->default('Pending');
            $table->date('approve_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained(config('hrm.tables.created_by'))->onDelete('no action');
            $table->foreignId('updated_by')->nullable()->constrained(config('hrm.tables.created_by'))->onDelete('no action');
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
        Schema::dropIfExists('employee_expense_bills');
    }
};
