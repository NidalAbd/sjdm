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
            $table->decimal('cost', 15, 7); // Increase precision and scale
            $table->decimal('rate', 15, 7); // Increase precision and scale
            $table->integer('min');
            $table->integer('max');
            $table->boolean('refill')->default(false);
            $table->boolean('cancel')->default(false);
            $table->timestamps();
        });

        // Added here (instead of the orders migration) because this table
        // must exist before the foreign key can reference it, and this
        // migration runs after create_orders_table.
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_service_id_foreign');
        });
        Schema::dropIfExists('services');
    }
};

