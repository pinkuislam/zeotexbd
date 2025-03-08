<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSMSModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_m_s_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile_number');
            $table->dateTime('datetime');
            $table->text('message')->nullable();
            $table->text('response')->nullable();
            $table->string('sent_by')->nullable();
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
        Schema::dropIfExists('s_m_s_models');
    }
}
