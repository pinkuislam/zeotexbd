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
        Schema::create('overtime_policies', function (Blueprint $table) {
            $table->id();
            $table->string("name", 255);
            $table->unsignedDecimal('amount');
            $table->enum('status', ['Active', 'Deactivated'])->default('Active');
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
        Schema::dropIfExists('overtime_policies');
    }
};
