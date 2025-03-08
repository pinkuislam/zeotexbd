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
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->enum('pay_type', ['Paid', 'Unpaid'])->default('Unpaid');
            $table->string("contact_number")->nullable();
            $table->text("purpose");
            $table->date("application_date");
            $table->date("start_date");
            $table->date("end_date");
            $table->unsignedTinyInteger("day_count")->default('1');
            $table->string("attachment")->nullable();
            $table->enum('status', ['Pending', 'Authorized', 'Approved'])->default('Pending');
            $table->foreignId('authorized_by')->nullable()->constrained(config('hrm.tables.created_by'))->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained(config('hrm.tables.created_by'))->nullOnDelete();
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
        Schema::dropIfExists('employee_leaves');
    }
};
