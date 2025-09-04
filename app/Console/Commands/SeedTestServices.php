<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class SeedTestServices extends Command
{
    protected $signature = 'services:seed-test {--count=50 : Number of test services to create}';
    protected $description = 'Create test services for development and SEO testing';

    public function handle()
    {
        $count = (int) $this->option('count');
        
        $this->info("Creating {$count} test services...");
        
        $platforms = ['Instagram', 'Facebook', 'TikTok', 'YouTube', 'Twitter'];
        $categories = ['Followers', 'Likes', 'Views', 'Comments', 'Shares'];
        
        for ($i = 1; $i <= $count; $i++) {
            $platform = $platforms[array_rand($platforms)];
            $category = $categories[array_rand($categories)];
            
            Service::create([
                'service_id' => 10000 + $i,
                'name_en' => "{$platform} {$category} Service #{$i}",
                'name_ar' => "خدمة {$platform} {$category} رقم {$i}",
                'category_en' => "{$platform} {$category}",
                'category_ar' => "{$platform} {$category}",
                'type' => 'default',
                'rate' => rand(1, 100) / 10, // Random rate between 0.1 and 10
                'cost' => rand(1, 50) / 10,   // Random cost between 0.1 and 5
                'min' => rand(100, 1000),
                'max' => rand(10000, 100000),
                'refill' => rand(0, 1),
                'cancel' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            if ($i % 10 === 0) {
                $this->line("Created {$i} services...");
            }
        }
        
        $this->info("✅ Successfully created {$count} test services!");
        $this->info("Total services in database: " . Service::count());
        
        return 0;
    }
}
