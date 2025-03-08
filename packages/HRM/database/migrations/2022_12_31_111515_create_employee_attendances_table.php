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
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date("attendance_date");
            $table->time("login_time")->nullable();
            $table->time("logout_time")->nullable();
            $table->double("in_latitude")->nullable();
            $table->double("out_latitude")->nullable();
            $table->double("in_longitude")->nullable();
            $table->double("out_longitude")->nullable();
            $table->text("in_address")->nullable();
            $table->text("out_address")->nullable();
            $table->text("in_note")->nullable();
            $table->text("out_note")->nullable();
            $table->string("in_image")->nullable();
            $table->string("in_image_url")->nullable();
            $table->string("out_image")->nullable();
            $table->string("out_image_url")->nullable();
            $table->enum('is_late', ['Yes', 'No'])->default('No');
            $table->enum('is_early', ['Yes', 'No'])->default('No');
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
        Schema::dropIfExists('employee_attendances');
    }
};
