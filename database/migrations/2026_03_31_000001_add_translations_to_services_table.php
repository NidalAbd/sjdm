<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // JSON column to store translations for all 17 languages
            // Structure: {"name": {"es": "...", "fr": "...", ...}, "category": {"es": "...", "fr": "...", ...}}
            $table->json('translations')->nullable()->after('category_ar');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('translations');
        });
    }
};
