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
            $table->unsignedBigInteger('service_id');
            $table->string('link');
            $table->integer('quantity');
            $table->integer('runs')->nullable();
            $table->decimal('charge', 10, 2)->nullable(); // Store the charge amount
            $table->string('status')->nullable(); // Store the status of the order
            $table->integer('interval')->nullable();
            $table->timestamps();

            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
