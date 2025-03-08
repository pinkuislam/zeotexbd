<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('mobile_2')->nullable();
            $table->string('email')->nullable();
            $table->datetime('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('role', userRoles())->default('Admin');
            $table->string('fcm_token')->nullable();
            $table->enum('status', ['Pending', 'Active', 'Deactivated'])->default('Pending');
            $table->string('image')->nullable();
            $table->string('image_url')->nullable();
            $table->string('fb_page_link')->nullable();
            $table->decimal('opening_due', 13, 2)->nullable();
            $table->string('address')->nullable();
            $table->string('color')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->default('Male');
            $table->string('nid_no')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
