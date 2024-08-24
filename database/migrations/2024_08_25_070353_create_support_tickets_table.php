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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->string('subject');
            $table->text('message');
            $table->unsignedBigInteger('status_id'); // Reference to ticket_statuses table
            $table->enum('type', ['order', 'payment']); // Enum type for ticket type
            $table->enum('subtype', [
                'refund',
                'acceleration',
                'cancel',
                'failed_payment',
                'refund_request',
                'payment_dispute',
                'chargeback',
                'invoice_request'
            ])->nullable(); // Enum type for ticket subtype, can be nullable
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('ticket_statuses')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
};
