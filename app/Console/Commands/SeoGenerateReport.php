<?php

namespace App\Console\Commands;

use App\Services\SeoReportingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeoGenerateReport extends Command
{
    protected $signature = 'seo:generate-report {type=daily : Report type (daily, weekly, monthly)}';
    protected $description = 'Generate SEO report';

    public function handle(SeoReportingService $reportingService)
    {
        $type = $this->argument('type');

        if (!in_array($type, ['daily', 'weekly', 'monthly'])) {
            $this->error('Invalid report type. Use: daily, weekly, or monthly');
            return Command::FAILURE;
        }

        $this->info("Generating {$type} SEO report...");

        try {
            switch ($type) {
                case 'daily':
                    $report = $reportingService->generateDailyReport();
                    break;
                case 'weekly':
                    $report = $reportingService->generateWeeklyReport();
                    break;
                case 'monthly':
                    $report = $reportingService->generateMonthlyReport();
                    break;
            }

            $this->info("Report generated successfully. ID: {$report->id}");

            // Display summary
            if (isset($report->keywords_data)) {
                $this->info('Total keywords: ' . ($report->keywords_data['total'] ?? 0));
                $this->info('Rising keywords: ' . ($report->keywords_data['rising'] ?? 0));
            }

            if (isset($report->technical_issues)) {
                $this->info('Open issues: ' . ($report->technical_issues['total_open'] ?? 0));
                $this->info('Critical issues: ' . ($report->technical_issues['critical'] ?? 0));
            }

            Log::info("{$type} SEO Report generated", ['report_id' => $report->id]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Report generation failed: ' . $e->getMessage());
            Log::error('SEO Report generation failed', ['type' => $type, 'error' => $e->getMessage()]);

            return Command::FAILURE;
        }
    }
}
