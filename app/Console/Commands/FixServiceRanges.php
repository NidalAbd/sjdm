<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class FixServiceRanges extends Command
{
    protected $signature = 'services:fix-ranges';
    protected $description = 'Fix invalid min/max ranges in services';

    public function handle()
    {
        $this->info('Fixing invalid min/max ranges...');
        
        $invalidServices = Service::where('min', '>', 'max')->get();
        
        if ($invalidServices->count() === 0) {
            $this->info('✅ No services with invalid ranges found');
            return 0;
        }
        
        $this->warn("Found {$invalidServices->count()} services with invalid ranges");
        
        foreach ($invalidServices as $service) {
            $oldMin = $service->min;
            $oldMax = $service->max;
            
            // Fix the range by swapping if min > max
            if ($service->min > $service->max) {
                $service->update([
                    'min' => $oldMax,
                    'max' => $oldMin
                ]);
                $this->line("Fixed service ID {$service->service_id}: min={$oldMin}->{$oldMax}, max={$oldMax}->{$oldMin}");
            }
        }
        
        $this->info('✅ All invalid ranges have been fixed');
        
        return 0;
    }
}
