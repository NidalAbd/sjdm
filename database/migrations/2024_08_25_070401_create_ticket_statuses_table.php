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
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // E.g., "Open", "In Progress", "Closed", etc.
            $table->timestamps();
        });

        // Added here (instead of the support_tickets migration) because this
        // table must exist before the foreign key can reference it, and this
        // migration runs after create_support_tickets_table.
        Schema::table('support_tickets', function (Blueprint $table) {
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
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropForeign('support_tickets_status_id_foreign');
        });
        Schema::dropIfExists('ticket_statuses');
    }
};
