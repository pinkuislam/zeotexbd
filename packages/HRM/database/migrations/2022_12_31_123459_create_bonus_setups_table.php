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
        Schema::create('bonus_setups', function (Blueprint $table) {
            $table->id();
            $table->string("title", 255);
            $table->enum('percent_type', ['Basic', 'Gross'])->default('Basic');
            $table->unsignedDecimal('percent')->default(100);
            $table->date('bonus_date');
            $table->enum('status', ['Active', 'Processed', 'Canceled'])->default('Active');
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
        Schema::dropIfExists('bonus_setups');
    }
};
