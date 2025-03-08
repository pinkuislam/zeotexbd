<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessory_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Purchase', 'Consume', 'Purchase Return'])->default('Purchase');
            $table->morphs('flagable');
            $table->foreignId('accessory_id')->constrained('accessories')->cascadeOnDelete();
            $table->unsignedBigInteger('quantity')->default(0);
            $table->unsignedBigInteger('used_quantity')->default(0);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('accessory_items');
    }
}
