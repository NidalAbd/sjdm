<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Payment method name (e.g., "Stripe", "PayPal")
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->string('type'); // Payment type (card, bank_transfer, crypto, wallet, etc.)
            $table->text('description')->nullable(); // Description for users
            $table->string('logo')->nullable(); // Path to payment method logo
            $table->boolean('is_active')->default(true); // Enable/disable payment method
            $table->decimal('min_amount', 10, 2)->default(0); // Minimum payment amount
            $table->decimal('max_amount', 10, 2)->nullable(); // Maximum payment amount (null = no limit)
            $table->decimal('processing_fee_fixed', 10, 2)->default(0); // Fixed processing fee
            $table->decimal('processing_fee_percentage', 5, 2)->default(0); // Percentage processing fee
            $table->string('currency', 3)->default('USD'); // Supported currency
            $table->integer('processing_time_min')->default(0); // Minimum processing time in minutes
            $table->integer('processing_time_max')->default(60); // Maximum processing time in minutes
            $table->json('supported_countries')->nullable(); // Supported countries (JSON array)
            $table->json('api_credentials')->nullable(); // API credentials (encrypted)
            $table->string('gateway_url')->nullable(); // Payment gateway URL
            $table->string('webhook_url')->nullable(); // Webhook URL for callbacks
            $table->text('instructions')->nullable(); // Payment instructions for users
            $table->integer('sort_order')->default(0); // Display order
            $table->boolean('requires_verification')->default(false); // Requires KYC/verification
            $table->json('settings')->nullable(); // Additional gateway-specific settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
}; 