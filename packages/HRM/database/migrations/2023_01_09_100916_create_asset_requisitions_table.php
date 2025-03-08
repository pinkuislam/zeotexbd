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
        Schema::create('asset_requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("employee_id");
            $table->date("expected_date")->nullable();
            $table->string("item");
            $table->text("note");
            $table->unsignedSmallInteger("quantity");
            $table->enum('status', ['Pending', 'Canceled', 'Approved'])->default('Pending');
            $table->text("feedback")->nullable();
            $table->unsignedBigInteger("updated_by")->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('asset_requisitions');
    }
};
