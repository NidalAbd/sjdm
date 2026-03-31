<?php

namespace App\Services;

use App\Models\SeoKeyword;
use App\Models\SeoPage;
use App\Models\SeoIssue;
use App\Models\SeoReport;
use App\Models\SeoContentIdea;
use App\Models\SeoActionLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeoReportingService
{
    /**
     * Generate daily SEO report
     */
    public function generateDailyReport()
    {
        $report = SeoReport::updateOrCreate(
            ['type' => 'daily', 'report_date' => today()],
            [
                'keywords_data' => $this->getKeywordsSummary(),
                'indexing_data' => $this->getIndexingSummary(),
                'technical_issues' => $this->getTechnicalIssuesSummary(),
                'performance_summary' => $this->getPerformanceSummary(),
            ]
        );

        SeoActionLog::log('report_generated', 'Daily SEO report generated', [
            'new_value' => ['report_id' => $report->id],
        ]);

        return $report;
    }

    /**
     * Generate weekly SEO report
     */
    public function generateWeeklyReport()
    {
        $report = SeoReport::updateOrCreate(
            ['type' => 'weekly', 'report_date' => today()],
            [
                'keywords_data' => $this->getWeeklyKeywordAnalysis(),
                'indexing_data' => $this->getIndexingSummary(),
                'technical_issues' => $this->getTechnicalIssuesSummary(),
                'content_suggestions' => $this->getContentSuggestions(),
                'trend_analysis' => $this->getTrendAnalysis(),
                'performance_summary' => $this->getWeeklyPerformance(),
            ]
        );

        SeoActionLog::log('report_generated', 'Weekly SEO report generated', [
            'new_value' => ['report_id' => $report->id],
        ]);

        return $report;
    }

    /**
     * Generate monthly SEO report
     */
    public function generateMonthlyReport()
    {
        $report = SeoReport::updateOrCreate(
            ['type' => 'monthly', 'report_date' => today()],
            [
                'keywords_data' => $this->getMonthlyKeywordAnalysis(),
                'indexing_data' => $this->getIndexingSummary(),
                'technical_issues' => $this->getMonthlyIssuesTrend(),
                'content_suggestions' => $this->getContentSuggestions(),
                'trend_analysis' => $this->getMonthlyTrendAnalysis(),
                'performance_summary' => $this->getMonthlyPerformance(),
            ]
        );

        SeoActionLog::log('report_generated', 'Monthly SEO report generated', [
            'new_value' => ['report_id' => $report->id],
        ]);

        return $report;
    }

    /**
     * Get keywords summary for daily report
     */
    protected function getKeywordsSummary()
    {
        return [
            'total' => SeoKeyword::active()->count(),
            'new_today' => SeoKeyword::whereDate('created_at', today())->count(),
            'rising' => SeoKeyword::active()->rising()->count(),
            'declining' => SeoKeyword::active()->declining()->count(),
            'opportunities' => SeoKeyword::active()->opportunities()->count(),
            'top_keywords' => SeoKeyword::active()
                ->orderByDesc('clicks')
                ->limit(10)
                ->get(['keyword', 'clicks', 'impressions', 'position', 'ctr'])
                ->toArray(),
            'position_changes' => SeoKeyword::active()
                ->whereNotNull('previous_position')
                ->whereRaw('ABS(position - previous_position) > 3')
                ->orderByRaw('position - previous_position')
                ->limit(10)
                ->get(['keyword', 'position', 'previous_position'])
                ->toArray(),
        ];
    }

    /**
     * Get indexing summary
     */
    protected function getIndexingSummary()
    {
        return [
            'total_pages' => SeoPage::count(),
            'indexed' => SeoPage::where('index_status', 'indexed')->count(),
            'not_indexed' => SeoPage::where('index_status', 'not_indexed')->count(),
            'crawled_not_indexed' => SeoPage::where('index_status', 'crawled_not_indexed')->count(),
            'in_sitemap' => SeoPage::where('in_sitemap', true)->count(),
            'recent_issues' => SeoIssue::whereIn('type', ['index_issue', 'crawl_error'])
                ->where('status', 'open')
                ->orderByDesc('detected_at')
                ->limit(5)
                ->get(['url', 'title', 'detected_at'])
                ->toArray(),
        ];
    }

    /**
     * Get technical issues summary
     */
    protected function getTechnicalIssuesSummary()
    {
        return [
            'total_open' => SeoIssue::where('status', 'open')->count(),
            'critical' => SeoIssue::where('status', 'open')->where('severity', 'critical')->count(),
            'warnings' => SeoIssue::where('status', 'open')->where('severity', 'warning')->count(),
            'by_type' => SeoIssue::where('status', 'open')
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'recent_issues' => SeoIssue::where('status', 'open')
                ->orderByRaw("FIELD(severity, 'critical', 'warning', 'info')")
                ->orderByDesc('detected_at')
                ->limit(10)
                ->get(['type', 'severity', 'title', 'url', 'detected_at'])
                ->toArray(),
            'resolved_today' => SeoIssue::whereDate('resolved_at', today())->count(),
        ];
    }

    /**
     * Get performance summary
     */
    protected function getPerformanceSummary()
    {
        $keywords = SeoKeyword::active()->fromSource('gsc');

        return [
            'total_impressions' => $keywords->sum('impressions'),
            'total_clicks' => $keywords->sum('clicks'),
            'avg_ctr' => round($keywords->avg('ctr'), 4),
            'avg_position' => round($keywords->avg('position'), 1),
            'core_web_vitals' => [
                'good_lcp' => SeoPage::where('lcp', '<', 2.5)->count(),
                'needs_improvement_lcp' => SeoPage::whereBetween('lcp', [2.5, 4])->count(),
                'poor_lcp' => SeoPage::where('lcp', '>', 4)->count(),
            ],
        ];
    }

    /**
     * Get weekly keyword analysis
     */
    protected function getWeeklyKeywordAnalysis()
    {
        $weekAgo = Carbon::now()->subWeek();

        return array_merge($this->getKeywordsSummary(), [
            'new_this_week' => SeoKeyword::where('created_at', '>=', $weekAgo)->count(),
            'biggest_gainers' => SeoKeyword::active()
                ->whereNotNull('previous_position')
                ->whereRaw('previous_position - position > 5')
                ->orderByRaw('previous_position - position DESC')
                ->limit(10)
                ->get(['keyword', 'position', 'previous_position', 'clicks'])
                ->toArray(),
            'biggest_losers' => SeoKeyword::active()
                ->whereNotNull('previous_position')
                ->whereRaw('position - previous_position > 5')
                ->orderByRaw('position - previous_position DESC')
                ->limit(10)
                ->get(['keyword', 'position', 'previous_position', 'clicks'])
                ->toArray(),
            'cluster_performance' => SeoKeyword::active()
                ->select('cluster',
                    DB::raw('SUM(clicks) as total_clicks'),
                    DB::raw('SUM(impressions) as total_impressions'),
                    DB::raw('AVG(position) as avg_position')
                )
                ->whereNotNull('cluster')
                ->groupBy('cluster')
                ->get()
                ->toArray(),
        ]);
    }

    /**
     * Get content suggestions
     */
    protected function getContentSuggestions()
    {
        return [
            'pending_ideas' => SeoContentIdea::pending()->count(),
            'high_priority' => SeoContentIdea::pending()
                ->where('priority', 'high')
                ->orderByDesc('estimated_traffic')
                ->limit(5)
                ->get(['title', 'target_keyword', 'content_type', 'estimated_traffic'])
                ->toArray(),
            'from_trends' => SeoContentIdea::pending()
                ->where('source', 'trends')
                ->limit(5)
                ->get(['title', 'target_keyword'])
                ->toArray(),
            'from_gaps' => SeoContentIdea::pending()
                ->where('source', 'competitor')
                ->limit(5)
                ->get(['title', 'target_keyword'])
                ->toArray(),
        ];
    }

    /**
     * Get trend analysis
     */
    protected function getTrendAnalysis()
    {
        return [
            'rising_keywords' => SeoKeyword::active()
                ->rising()
                ->orderByDesc('trend_score')
                ->limit(15)
                ->get(['keyword', 'trend_score', 'search_volume'])
                ->toArray(),
            'seasonal_trends' => $this->getSeasonalTrends(),
            'emerging_topics' => SeoKeyword::active()
                ->where('source', 'trends')
                ->where('created_at', '>=', Carbon::now()->subWeek())
                ->orderByDesc('trend_score')
                ->limit(10)
                ->get(['keyword', 'trend_score'])
                ->toArray(),
        ];
    }

    /**
     * Get weekly performance metrics
     */
    protected function getWeeklyPerformance()
    {
        $summary = $this->getPerformanceSummary();

        // Compare with previous week
        $previousWeekReport = SeoReport::where('type', 'weekly')
            ->where('report_date', '<', today())
            ->orderByDesc('report_date')
            ->first();

        if ($previousWeekReport && isset($previousWeekReport->performance_summary)) {
            $prev = $previousWeekReport->performance_summary;
            $summary['impressions_change'] = $this->calculateChange(
                $summary['total_impressions'],
                $prev['total_impressions'] ?? 0
            );
            $summary['clicks_change'] = $this->calculateChange(
                $summary['total_clicks'],
                $prev['total_clicks'] ?? 0
            );
            $summary['position_change'] = round(
                ($prev['avg_position'] ?? 0) - $summary['avg_position'],
                1
            );
        }

        return $summary;
    }

    /**
     * Get monthly keyword analysis
     */
    protected function getMonthlyKeywordAnalysis()
    {
        $monthAgo = Carbon::now()->subMonth();

        return array_merge($this->getWeeklyKeywordAnalysis(), [
            'new_this_month' => SeoKeyword::where('created_at', '>=', $monthAgo)->count(),
            'intent_distribution' => SeoKeyword::active()
                ->select('intent', DB::raw('count(*) as count'))
                ->whereNotNull('intent')
                ->groupBy('intent')
                ->pluck('count', 'intent')
                ->toArray(),
            'long_tail_performance' => [
                'count' => SeoKeyword::active()->longTail()->count(),
                'avg_clicks' => SeoKeyword::active()->longTail()->avg('clicks'),
                'avg_position' => SeoKeyword::active()->longTail()->avg('position'),
            ],
        ]);
    }

    /**
     * Get monthly issues trend
     */
    protected function getMonthlyIssuesTrend()
    {
        $summary = $this->getTechnicalIssuesSummary();

        // Issues resolved this month
        $summary['resolved_this_month'] = SeoIssue::where('resolved_at', '>=', Carbon::now()->subMonth())->count();

        // New issues this month
        $summary['new_this_month'] = SeoIssue::where('detected_at', '>=', Carbon::now()->subMonth())->count();

        // Trend by week
        $summary['weekly_trend'] = [];
        for ($i = 4; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

            $summary['weekly_trend'][] = [
                'week' => $weekStart->format('M d'),
                'new' => SeoIssue::whereBetween('detected_at', [$weekStart, $weekEnd])->count(),
                'resolved' => SeoIssue::whereBetween('resolved_at', [$weekStart, $weekEnd])->count(),
            ];
        }

        return $summary;
    }

    /**
     * Get monthly trend analysis
     */
    protected function getMonthlyTrendAnalysis()
    {
        $analysis = $this->getTrendAnalysis();

        // Add monthly specific data
        $analysis['keyword_growth'] = $this->getKeywordGrowthTrend();
        $analysis['top_performing_clusters'] = SeoKeyword::active()
            ->select('cluster',
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('COUNT(*) as keyword_count')
            )
            ->whereNotNull('cluster')
            ->groupBy('cluster')
            ->orderByDesc('total_clicks')
            ->limit(5)
            ->get()
            ->toArray();

        return $analysis;
    }

    /**
     * Get monthly performance
     */
    protected function getMonthlyPerformance()
    {
        $summary = $this->getPerformanceSummary();

        // Weekly breakdown
        $summary['weekly_breakdown'] = [];
        for ($i = 4; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();

            $report = SeoReport::where('type', 'weekly')
                ->where('report_date', '>=', $weekStart)
                ->where('report_date', '<', $weekStart->copy()->addWeek())
                ->first();

            $summary['weekly_breakdown'][] = [
                'week' => $weekStart->format('M d'),
                'clicks' => $report->performance_summary['total_clicks'] ?? 0,
                'impressions' => $report->performance_summary['total_impressions'] ?? 0,
            ];
        }

        return $summary;
    }

    /**
     * Get seasonal trends
     */
    protected function getSeasonalTrends()
    {
        // This would analyze historical data to identify seasonal patterns
        // For now, return mock seasonal keywords
        return [
            ['keyword' => 'holiday promotion', 'peak_month' => 'December'],
            ['keyword' => 'summer sale', 'peak_month' => 'June'],
            ['keyword' => 'black friday deals', 'peak_month' => 'November'],
        ];
    }

    /**
     * Get keyword growth trend
     */
    protected function getKeywordGrowthTrend()
    {
        $trend = [];

        for ($i = 4; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

            $trend[] = [
                'week' => $weekStart->format('M d'),
                'new_keywords' => SeoKeyword::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'total_active' => SeoKeyword::active()
                    ->where('created_at', '<=', $weekEnd)
                    ->count(),
            ];
        }

        return $trend;
    }

    /**
     * Calculate percentage change
     */
    protected function calculateChange($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Get action log for today
     */
    public function getTodayActions()
    {
        return SeoActionLog::today()
            ->orderByDesc('created_at')
            ->get(['action_type', 'description', 'url', 'status', 'created_at'])
            ->toArray();
    }

    /**
     * Get report history
     */
    public function getReportHistory($type = 'daily', $limit = 30)
    {
        return SeoReport::where('type', $type)
            ->orderByDesc('report_date')
            ->limit($limit)
            ->get(['id', 'type', 'report_date', 'created_at']);
    }
}
