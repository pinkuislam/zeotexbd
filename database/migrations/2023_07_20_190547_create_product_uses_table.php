<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_in_id')->constrained('product_ins')->cascadeOnDelete();
            $table->foreignId('product_out_id')->constrained('product_outs')->cascadeOnDelete();
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
        Schema::dropIfExists('product_uses');
    }
}
