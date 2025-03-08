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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('year', 4);
            $table->string('month', 2);
            $table->date('salary_date');
            $table->unsignedDecimal('gross_salary');
            $table->unsignedDecimal('mobile_bill')->nullable();
            $table->unsignedDecimal('overtime_amount')->nullable();
            $table->unsignedDecimal('bonus_amount')->nullable();
            $table->unsignedDecimal('advance_amount')->nullable();
            $table->unsignedDecimal('consider_amount')->nullable();
            $table->unsignedDecimal('incentive_amount')->nullable();
            $table->unsignedDecimal('penalty_amount')->nullable();
            $table->unsignedDecimal('pf_deduction')->nullable();
            $table->unsignedDecimal('expense_amount')->nullable();
            $table->unsignedDecimal('income_tax')->nullable();
            $table->unsignedDecimal('net_salary');
            $table->enum('status', ['Processed', 'Paid', 'Hold'])->default('Processed');
            $table->foreignId('created_by')->nullable()->constrained(config('hrm.tables.created_by'))->onDelete('no action');
            $table->foreignId('updated_by')->nullable()->constrained(config('hrm.tables.created_by'))->onDelete('no action');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
};
