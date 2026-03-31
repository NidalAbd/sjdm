<?php

namespace App\Console\Commands;

use App\Services\SeoAutomationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeoGenerateContent extends Command
{
    protected $signature = 'seo:generate-content';
    protected $description = 'Generate content ideas based on keyword analysis';

    public function handle(SeoAutomationService $seoService)
    {
        $this->info('Generating content ideas...');

        try {
            // Generate content ideas
            $ideas = $seoService->generateContentIdeas();

            $this->info('Generated ' . count($ideas) . ' content ideas');

            // Get long-tail opportunities
            $longTail = $seoService->findLongTailOpportunities();
            $this->info('Found ' . count($longTail) . ' long-tail opportunities');

            // Get replacement suggestions for weak keywords
            $replacements = $seoService->suggestReplacementKeywords();
            $this->info('Found ' . count($replacements) . ' keyword replacement suggestions');

            Log::info('SEO Content Generation completed', [
                'ideas' => count($ideas),
                'long_tail' => count($longTail),
                'replacements' => count($replacements),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Content generation failed: ' . $e->getMessage());
            Log::error('SEO Content Generation failed', ['error' => $e->getMessage()]);

            return Command::FAILURE;
        }
    }
}
