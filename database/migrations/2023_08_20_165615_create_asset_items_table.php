<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_items', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('note')->nullable();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->decimal('price', 14, 2);
            $table->decimal('total_amount', 14, 2);
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
        Schema::dropIfExists('asset_items');
    }
}
