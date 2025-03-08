<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoryItemUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessory_item_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accessory_in_id')->constrained('accessory_items')->cascadeOnDelete();
            $table->foreignId('accessory_out_id')->constrained('accessory_items')->cascadeOnDelete();
            $table->unsignedBigInteger('quantity');
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
        Schema::dropIfExists('accessory_item_uses');
    }
}
