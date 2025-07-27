<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup storage directories for shared hosting without symbolic links';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up storage directories for shared hosting...');

        // Define paths
        $publicHtmlPath = base_path('public_html');
        $storagePath = $publicHtmlPath . '/storage';
        $appPublicPath = $storagePath . '/app/public';
        $paymentMethodsPath = $appPublicPath . '/payment-methods';
        $imagesPath = $publicHtmlPath . '/images/payment-methods';

        // Create directories
        $directories = [
            $storagePath,
            $storagePath . '/framework/cache',
            $storagePath . '/framework/sessions',
            $storagePath . '/framework/testing',
            $storagePath . '/framework/views',
            $storagePath . '/logs',
            $appPublicPath,
            $paymentMethodsPath,
            $imagesPath,
        ];

        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                if (File::makeDirectory($dir, 0755, true)) {
                    $this->info("Created directory: $dir");
                } else {
                    $this->error("Failed to create directory: $dir");
                }
            } else {
                $this->line("Directory already exists: $dir");
            }
        }

        // Set permissions
        $this->info('Setting permissions...');
        system("chmod -R 755 $storagePath");
        system("chmod -R 777 $paymentMethodsPath");
        system("chmod -R 755 $imagesPath");

        // Create default logo
        $defaultLogo = $imagesPath . '/default.svg';
        if (!File::exists($defaultLogo)) {
            $svgContent = '<svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect width="64" height="64" rx="8" fill="#f8f9fa"/>
  <rect x="12" y="20" width="40" height="24" rx="4" fill="#6c757d"/>
  <rect x="16" y="24" width="32" height="16" rx="2" fill="#ffffff"/>
  <rect x="20" y="28" width="8" height="8" rx="2" fill="#6c757d"/>
  <rect x="32" y="28" width="12" height="8" rx="2" fill="#6c757d"/>
  <rect x="20" y="40" width="24" height="2" rx="1" fill="#6c757d"/>
</svg>';
            
            if (File::put($defaultLogo, $svgContent)) {
                $this->info("Created default logo: $defaultLogo");
            } else {
                $this->error("Failed to create default logo: $defaultLogo");
            }
        } else {
            $this->line("Default logo already exists: $defaultLogo");
        }

        // Create test file
        $testFile = $paymentMethodsPath . '/test.txt';
        if (!File::exists($testFile)) {
            if (File::put($testFile, 'test')) {
                $this->info("Created test file: $testFile");
            } else {
                $this->error("Failed to create test file: $testFile");
            }
        } else {
            $this->line("Test file already exists: $testFile");
        }

        $this->info('Storage setup completed!');
        $this->info('Test URL: ' . config('app.url') . '/storage/app/public/payment-methods/test.txt');
    }
}
