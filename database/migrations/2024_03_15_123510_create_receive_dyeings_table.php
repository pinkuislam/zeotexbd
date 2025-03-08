<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiveDyeingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receive_dyeings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dyeing_agent_id')->nullable()->constrained('dyeing_agents')->nullOnDelete();
            $table->string('code')->unique();
            $table->date('date');
            $table->text('note')->nullable();
            $table->decimal('cost_per_unit', 8, 2)->default(0);
            $table->decimal('total_cost', 13, 2)->default(0);
            $table->decimal('grey_fabric_consume', 12, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('receive_dyeings');
    }
}
