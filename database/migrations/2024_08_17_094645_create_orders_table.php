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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_id');
            $table->string('link');
            $table->integer('quantity');
            $table->integer('start_count')->nullable();
            $table->string('remains')->nullable();
            $table->string('runs')->nullable();
            $table->decimal('charge', 10, 2)->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('api_order_id')->nullable();
            $table->integer('interval')->nullable();
            $table->boolean('can_refill')->default(false); // New column for refill capability
            $table->boolean('can_cancel')->default(false); // New column for cancel capability
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
