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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('employee_no', 150)->unique();
            $table->string('father_name', 150);
            $table->string('mother_name', 150);
            $table->text("remarks")->nullable();
            $table->date('birth_date')->nullable();
            $table->enum("gender", ['Male', 'Female', 'Other'])->default("Male");
            $table->date("org_joining_date")->nullable();
            $table->enum("religion", ['Islam', 'Hinduism', 'Christian', 'Buddhism'])->default('Islam');
            $table->string("nationality", 100);
            $table->string("blood_group", 3)->nullable();
            $table->string('contact_no', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string("image")->nullable();
            $table->string("nid_front_image")->nullable();
            $table->string("nid_back_image")->nullable();
            $table->enum("status", ['Active', 'Deactivated'])->default("Active");
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
        Schema::dropIfExists('employees');
    }
};
