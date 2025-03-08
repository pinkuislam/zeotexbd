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
        Schema::create('employee_salary_setups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedDecimal('basic_salary')->default(0);
            $table->unsignedDecimal('house_rent')->default(0);
            $table->unsignedDecimal('medical_allowance')->default(0);
            $table->unsignedDecimal('conveyance_allowance')->default(0);
            $table->unsignedDecimal('entertainment_allowance')->default(0);
            $table->unsignedDecimal('other_allowance')->default(0);
            $table->unsignedDecimal('income_tax')->default(0);
            $table->unsignedDecimal('pf_deduction')->default(0)->comment('percentage of basic Salary');
            $table->unsignedDecimal('mobile_bill')->default(0);
            $table->unsignedDecimal('gross_salary', 10, 2);
            $table->string('bank_acc_no', 50)->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
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
        Schema::dropIfExists('employee_salary_setups');
    }
};
