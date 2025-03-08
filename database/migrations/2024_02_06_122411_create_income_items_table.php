<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('income_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_id')->constrained('incomes')->cascadeOnDelete();            
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();            
            $table->foreignId('income_category_id')->constrained('income_categories')->cascadeOnDelete();
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_items');
    }
};
