<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Add this line
            $table->unsignedBigInteger('service_id');
            $table->string('link');
            $table->integer('quantity');
            $table->integer('start_count')->nullable();
            $table->integer('remains')->nullable();
            $table->integer('runs')->nullable();
            $table->decimal('charge', 10, 2)->nullable(); // Store the charge amount
            $table->string('status')->nullable(); // Store the status of the order
            $table->unsignedBigInteger('api_order_id')->nullable();
            $table->integer('interval')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
