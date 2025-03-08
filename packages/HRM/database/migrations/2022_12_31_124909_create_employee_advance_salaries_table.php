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
        Schema::create('employee_advance_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->date('date');
            $table->text('note')->nullable();
            $table->unsignedDecimal('amount', 10, 2);
            $table->enum('deduct_type', ['onetime', 'installment'])->default('onetime');
            $table->integer('installment_count')->default(1);
            $table->date('deduct_start_on');
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
        Schema::dropIfExists('employee_advance_salaries');
    }
};
