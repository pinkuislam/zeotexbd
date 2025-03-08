<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryAgentPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_agent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_agent_id')->constrained('delivery_agents')->cascadeOnDelete();
            $table->enum('type', ['Payment', 'Adjustment', 'Received'])->default('Payment');
            $table->date('date');
            $table->string('receipt_no');
            $table->decimal('total_amount', 10, 2);
            $table->text('note')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('delivery_agent_payments');
    }
}
