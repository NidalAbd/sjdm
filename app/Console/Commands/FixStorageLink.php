<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixStorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix storage link for shared hosting environments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Fixing storage link for shared hosting...');

        // Check if public_html directory exists
        $publicHtmlPath = base_path('public_html');
        if (!File::exists($publicHtmlPath)) {
            $this->error('public_html directory not found. This command is for shared hosting environments.');
            return 1;
        }

        // Remove existing storage link if it exists
        $storageLinkPath = $publicHtmlPath . '/storage';
        if (File::exists($storageLinkPath)) {
            $this->info('Removing existing storage link...');
            File::delete($storageLinkPath);
        }

        // Create new symbolic link
        $targetPath = storage_path('app/public');
        $linkPath = $storageLinkPath;

        $this->info("Creating symbolic link from {$linkPath} to {$targetPath}...");

        try {
            // Create the symbolic link
            symlink($targetPath, $linkPath);
            $this->info('Storage link created successfully!');
            
            // Test the link
            if (File::exists($linkPath)) {
                $this->info('Link verification successful.');
                $this->info('You can now access files at: ' . url('/storage'));
            } else {
                $this->error('Link creation failed.');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Failed to create storage link: ' . $e->getMessage());
            $this->info('Manual steps:');
            $this->info('1. Navigate to your domain directory');
            $this->info('2. Run: rm -f public_html/storage');
            $this->info('3. Run: ln -s ../storage/app/public public_html/storage');
            return 1;
        }

        return 0;
    }
} 