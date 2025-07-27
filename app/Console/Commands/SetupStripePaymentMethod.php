<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use Illuminate\Console\Command;

class SetupStripePaymentMethod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:setup-stripe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up Stripe as the first payment method';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Stripe payment method...');

        // Check if Stripe already exists
        $existingStripe = PaymentMethod::where('slug', 'stripe')->first();

        if ($existingStripe) {
            $this->warn('Stripe payment method already exists with ID: ' . $existingStripe->id);
            
            if ($this->confirm('Do you want to update the existing Stripe configuration?')) {
                $this->updateStripeMethod($existingStripe);
            } else {
                $this->info('Skipping Stripe setup.');
                return;
            }
        } else {
            $this->createStripeMethod();
        }

        $this->info('Stripe payment method setup completed!');
    }

    private function createStripeMethod()
    {
        $this->info('Creating new Stripe payment method...');

        $stripeMethod = PaymentMethod::create([
            'name' => 'Stripe',
            'slug' => 'stripe',
            'type' => 'credit_card',
            'description' => 'Accept credit and debit cards worldwide with Stripe payment processing.',
            'is_active' => true,
            'min_amount' => 10.00,
            'max_amount' => 10000.00,
            'processing_fee_fixed' => 0.30,
            'processing_fee_percentage' => 2.9,
            'currency' => 'USD',
            'processing_time_min' => 0,
            'processing_time_max' => 5,
            'gateway_url' => 'https://api.stripe.com',
            'webhook_url' => '/webhook/stripe',
            'instructions' => 'Enter your card details securely. Your payment will be processed instantly.',
            'sort_order' => 1,
            'requires_verification' => false,
            'supported_countries' => ['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'AE', 'SA', 'EG'],
        ]);

        $this->info('Stripe payment method created with ID: ' . $stripeMethod->id);

        // Try to add API credentials if they exist in .env
        $this->addApiCredentials($stripeMethod);
    }

    private function updateStripeMethod($stripeMethod)
    {
        $this->info('Updating existing Stripe payment method...');

        $stripeMethod->update([
            'name' => 'Stripe',
            'type' => 'credit_card',
            'description' => 'Accept credit and debit cards worldwide with Stripe payment processing.',
            'is_active' => true,
            'min_amount' => 10.00,
            'max_amount' => 10000.00,
            'processing_fee_fixed' => 0.30,
            'processing_fee_percentage' => 2.9,
            'currency' => 'USD',
            'processing_time_min' => 0,
            'processing_time_max' => 5,
            'gateway_url' => 'https://api.stripe.com',
            'webhook_url' => '/webhook/stripe',
            'instructions' => 'Enter your card details securely. Your payment will be processed instantly.',
            'sort_order' => 1,
            'requires_verification' => false,
            'supported_countries' => ['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'AE', 'SA', 'EG'],
        ]);

        $this->info('Stripe payment method updated successfully!');

        // Try to add API credentials if they exist in .env
        $this->addApiCredentials($stripeMethod);
    }

    private function addApiCredentials($stripeMethod)
    {
        $stripeKey = env('STRIPE_KEY');
        $stripeSecret = env('STRIPE_SECRET');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        if (!$stripeKey && !$stripeSecret && !$webhookSecret) {
            $this->warn('No Stripe environment variables found. You can add them later in the admin panel.');
            $this->warn('Expected environment variables: STRIPE_KEY, STRIPE_SECRET, STRIPE_WEBHOOK_SECRET');
            return;
        }

        $credentials = [];
        if ($stripeKey) {
            $credentials['api_key'] = $stripeKey;
            $this->info('✓ Found STRIPE_KEY');
        }
        if ($stripeSecret) {
            $credentials['api_secret'] = $stripeSecret;
            $this->info('✓ Found STRIPE_SECRET');
        }
        if ($webhookSecret) {
            $credentials['webhook_secret'] = $webhookSecret;
            $this->info('✓ Found STRIPE_WEBHOOK_SECRET');
        }

        try {
            $stripeMethod->api_credentials = $credentials;
            $stripeMethod->save();
            $this->info('✓ API credentials saved successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to save API credentials: ' . $e->getMessage());
            $this->warn('You can add the API credentials manually in the admin panel.');
        }
    }
}
