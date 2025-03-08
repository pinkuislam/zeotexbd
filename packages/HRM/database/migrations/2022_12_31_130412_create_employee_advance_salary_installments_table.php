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
        Schema::create('employee_advance_salary_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('advance_salary_id')->constrained('employee_advance_salaries', 'id', 'employee_advance_salary_installments_advance_salary_id_foreign')->cascadeOnDelete();
            $table->date('deduct_on');
            $table->unsignedDecimal('deduct_amount', 10, 2);
            $table->enum('status', ['Pending', 'Processed'])->default('Pending');
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
        Schema::dropIfExists('employee_advance_salary_installments');
    }
};
