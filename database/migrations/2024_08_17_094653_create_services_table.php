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
        Schema::create('services', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->primary();
            $table->string('name_en', 500)->default(''); // Default empty string
            $table->string('name_ar', 500)->default(''); // Default empty string
            $table->string('type');
            $table->string('category_en')->default(''); // Default empty string
            $table->string('category_ar')->default(''); // Default empty string
            $table->decimal('rate', 15, 5); // Increase precision and scale
            $table->integer('min');
            $table->integer('max');
            $table->boolean('refill')->default(false);
            $table->boolean('cancel')->default(false);
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
        Schema::dropIfExists('services');
    }
};

