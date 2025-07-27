<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Clear any corrupted API credentials data
        // This will allow users to re-enter their API credentials properly
        DB::table('payment_methods')
            ->whereNotNull('api_credentials')
            ->update(['api_credentials' => null]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No rollback needed - this is a data fix
    }
};
