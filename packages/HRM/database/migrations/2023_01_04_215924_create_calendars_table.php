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
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->time('working_hours')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['Open', 'Closed'])->default('Open');
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
        Schema::dropIfExists('calendars');
    }
};
