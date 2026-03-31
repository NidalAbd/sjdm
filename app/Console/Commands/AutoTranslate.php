<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Services\AutoTranslationService;
use Illuminate\Console\Command;

class AutoTranslate extends Command
{
    protected $signature = 'translate:auto {locale} {--language-name= : Target language name} {--tier=all : Tier to translate (1=UI, 2=services, all)} {--limit=0 : Limit for services}';
    protected $description = 'Auto-translate UI strings and service data to a target language';

    public function handle(AutoTranslationService $service)
    {
        $locale = $this->argument('locale');
        $tier = $this->option('tier');
        $limit = (int) $this->option('limit');

        $language = Language::getByCode($locale);
        if (!$language) {
            $this->error("Language '{$locale}' not found in database. Run migrations first.");
            return 1;
        }

        $languageName = $this->option('language-name') ?: $language->name;

        $this->info("Translating to {$languageName} ({$locale})...");

        if ($tier === 'all' || $tier === '1') {
            $this->info('Tier 1: UI Strings...');
            $result = $service->translateTier1($locale, $languageName);
            $this->info("  Completed: {$result['completed']}, Errors: {$result['errors']}");
        }

        if ($tier === 'all' || $tier === '2') {
            $this->info('Tier 2: Service Data...');
            $result = $service->translateServices($locale, $languageName, $limit ?: null);
            $this->info("  Completed: {$result['completed']}, Errors: {$result['errors']}");
        }

        $this->info('Done!');
        return 0;
    }
}
