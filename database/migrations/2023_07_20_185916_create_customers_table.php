<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('shipping_address')->nullable();
            $table->decimal('opening_due', 14, 2)->nullable()->default(0);
            $table->foreignId('shipping_rate_id')->nullable()->constrained('shipping_rates')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['Admin', 'Seller','Reseller'])->default('Admin');
            $table->enum('status', ['Active', 'Deactivated'])->default('Active');
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
        Schema::dropIfExists('customers');
    }
}
