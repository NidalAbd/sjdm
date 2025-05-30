<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Additional Fields
            $table->decimal('balance', 15, 7)->default(0.00);
            $table->string('currency', 3)->default('USD');
            $table->integer('points')->default(0); // Add points field to users

            $table->string('language', 5)->default('en');
            $table->enum('status', ['active', 'inactive', 'suspended', 'banned'])->default('inactive');
            // Personal Fields
            $table->enum('gender', ['male', 'female', 'other'])->nullable(); // Gender
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable(); // Marital Status
            $table->date('date_of_birth')->nullable(); // Date of Birth
            $table->string('referral_code')->unique()->nullable();
            $table->unsignedBigInteger('referred_by')->nullable()->index();
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps and Soft Deletes
            $table->timestamps();
            $table->softDeletes();
            $table->rememberToken();
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
};
