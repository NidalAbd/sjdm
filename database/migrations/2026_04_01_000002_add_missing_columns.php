<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add is_active to services if missing
        if (!Schema::hasColumn('services', 'is_active')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('cancel');
            });
        }

        // Add api_charge to orders if missing
        if (!Schema::hasColumn('orders', 'api_charge')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('api_charge', 15, 7)->default(0)->after('charge');
            });
        }

        // Add api_rate to services if missing
        if (!Schema::hasColumn('services', 'api_rate')) {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('api_rate', 15, 7)->default(0)->after('rate');
            });
        }

        // Add description to services if missing
        if (!Schema::hasColumn('services', 'description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->text('description')->nullable()->after('type');
            });
        }

        // Add dripfeed to services if missing
        if (!Schema::hasColumn('services', 'dripfeed')) {
            Schema::table('services', function (Blueprint $table) {
                $table->boolean('dripfeed')->default(false)->after('cancel');
            });
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'is_active')) $table->dropColumn('is_active');
            if (Schema::hasColumn('services', 'api_rate')) $table->dropColumn('api_rate');
            if (Schema::hasColumn('services', 'description')) $table->dropColumn('description');
            if (Schema::hasColumn('services', 'dripfeed')) $table->dropColumn('dripfeed');
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'api_charge')) $table->dropColumn('api_charge');
        });
    }
};
