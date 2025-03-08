<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('account_name');
            $table->string('account_no');
            $table->string('bank_name');
            $table->string('branch_name')->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->enum('status', ['Active', 'Deactivated'])->default('Active');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('banks');
    }
}
