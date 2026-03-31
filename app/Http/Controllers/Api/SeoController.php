<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeoKeyword;
use App\Models\SeoPage;
use App\Models\SeoIssue;
use App\Models\SeoContentIdea;
use App\Models\SeoActionLog;
use App\Models\SeoReport;
use App\Models\SeoStructuredData;
use App\Services\GoogleSearchConsoleService;
use App\Services\GoogleTrendsService;
use App\Services\SerpApiService;
use App\Services\SeoAutomationService;
use App\Services\TechnicalSeoService;
use App\Services\SeoReportingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SeoController extends Controller
{
    protected $gscService;
    protected $trendsService;
    protected $serpService;
    protected $automationService;
    protected $technicalService;
    protected $reportingService;

    public function __construct(
        GoogleSearchConsoleService $gscService,
        GoogleTrendsService $trendsService,
        SerpApiService $serpService,
        SeoAutomationService $automationService,
        TechnicalSeoService $technicalService,
        SeoReportingService $reportingService
    ) {
        $this->gscService = $gscService;
        $this->trendsService = $trendsService;
        $this->serpService = $serpService;
        $this->automationService = $automationService;
        $this->technicalService = $technicalService;
        $this->reportingService = $reportingService;
    }

    /**
     * Get comprehensive SEO Dashboard overview
     */
    public function dashboard()
    {
        $keywordStats = $this->automationService->getKeywordAnalytics();
        $gscStats = $this->gscService->getSummaryStats();
        $trendsSummary = $this->trendsService->getTrendsSummary();

        // Technical issues summary
        $issues = [
            'total_open' => SeoIssue::where('status', 'open')->count(),
            'critical' => SeoIssue::where('status', 'open')->where('severity', 'critical')->count(),
            'warnings' => SeoIssue::where('status', 'open')->where('severity', 'warning')->count(),
        ];

        // Pages summary
        $pages = [
            'total' => SeoPage::count(),
            'indexed' => SeoPage::where('index_status', 'indexed')->count(),
            'not_indexed' => SeoPage::whereIn('index_status', ['not_indexed', 'crawled_not_indexed'])->count(),
        ];

        // Content ideas summary
        $contentIdeas = [
            'pending' => SeoContentIdea::pending()->count(),
            'high_priority' => SeoContentIdea::pending()->highPriority()->count(),
        ];

        // Today's actions
        $todayActions = SeoActionLog::today()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'keywords' => $keywordStats,
                'gsc' => $gscStats,
                'trends' => $trendsSummary,
                'issues' => $issues,
                'pages' => $pages,
                'content_ideas' => $contentIdeas,
                'actions_today' => $todayActions,
                'last_updated' => now()->toIso8601String(),
            ]
        ]);
    }

    /**
     * Get all keywords with advanced filtering
     */
    public function keywords(Request $request)
    {
        $source = $request->get('source', 'all');
        $intent = $request->get('intent');
        $cluster = $request->get('cluster');
        $status = $request->get('status', 'active');
        $limit = $request->get('limit', 100);
        $sortBy = $request->get('sort_by', 'clicks');
        $sortOrder = $request->get('sort_order', 'desc');
        $filter = $request->get('filter'); // rising, declining, opportunity, long_tail

        $query = SeoKeyword::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($source !== 'all') {
            $query->where('source', $source);
        }

        if ($intent) {
            $query->where('intent', $intent);
        }

        if ($cluster) {
            $query->where('cluster', $cluster);
        }

        // Special filters
        if ($filter === 'rising') {
            $query->where('is_rising', true);
        } elseif ($filter === 'declining') {
            $query->where('is_declining', true);
        } elseif ($filter === 'opportunity') {
            $query->where('is_opportunity', true);
        } elseif ($filter === 'long_tail') {
            $query->where('is_long_tail', true);
        }

        $keywords = $query->orderBy($sortBy, $sortOrder)
            ->limit($limit)
            ->get();

        // Get clusters for filter dropdown
        $clusters = SeoKeyword::active()
            ->whereNotNull('cluster')
            ->distinct()
            ->pluck('cluster');

        return response()->json([
            'success' => true,
            'data' => $keywords,
            'meta' => [
                'total' => $keywords->count(),
                'clusters' => $clusters,
                'intents' => ['transactional', 'commercial', 'informational', 'navigational'],
            ]
        ]);
    }

    /**
     * Get keyword opportunities
     */
    public function keywordOpportunities()
    {
        $opportunities = SeoKeyword::active()
            ->opportunities()
            ->orderByDesc('search_volume')
            ->limit(50)
            ->get();

        $longTail = $this->automationService->findLongTailOpportunities();
        $replacements = $this->automationService->suggestReplacementKeywords();

        return response()->json([
            'success' => true,
            'data' => [
                'opportunities' => $opportunities,
                'long_tail_suggestions' => $longTail,
                'replacement_suggestions' => $replacements,
            ]
        ]);
    }

    /**
     * Get technical SEO issues
     */
    public function technicalIssues(Request $request)
    {
        $status = $request->get('status', 'open');
        $type = $request->get('type');
        $severity = $request->get('severity');

        $query = SeoIssue::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($severity) {
            $query->where('severity', $severity);
        }

        $issues = $query->orderByRaw("FIELD(severity, 'critical', 'warning', 'info')")
            ->orderByDesc('detected_at')
            ->paginate(50);

        // Get issue types for filter
        $types = SeoIssue::distinct()->pluck('type');

        return response()->json([
            'success' => true,
            'data' => $issues->items(),
            'meta' => [
                'total' => $issues->total(),
                'types' => $types,
                'severities' => ['critical', 'warning', 'info'],
            ],
            'pagination' => [
                'current_page' => $issues->currentPage(),
                'last_page' => $issues->lastPage(),
                'per_page' => $issues->perPage(),
            ]
        ]);
    }

    /**
     * Resolve an SEO issue
     */
    public function resolveIssue(Request $request, $id)
    {
        $issue = SeoIssue::findOrFail($id);
        $issue->resolve();

        SeoActionLog::log('issue_resolved', "Resolved issue: {$issue->title}", [
            'url' => $issue->url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Issue resolved successfully',
        ]);
    }

    /**
     * Get pages with SEO data
     */
    public function pages(Request $request)
    {
        $indexStatus = $request->get('index_status');
        $withIssues = $request->get('with_issues', false);

        $query = SeoPage::query();

        if ($indexStatus) {
            $query->where('index_status', $indexStatus);
        }

        if ($withIssues) {
            $query->withIssues();
        }

        $pages = $query->orderByDesc('impressions')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $pages->items(),
            'meta' => [
                'total' => $pages->total(),
                'indexed' => SeoPage::where('index_status', 'indexed')->count(),
                'not_indexed' => SeoPage::whereIn('index_status', ['not_indexed', 'crawled_not_indexed'])->count(),
            ],
            'pagination' => [
                'current_page' => $pages->currentPage(),
                'last_page' => $pages->lastPage(),
            ]
        ]);
    }

    /**
     * Get structured data status
     */
    public function structuredData(Request $request)
    {
        $type = $request->get('type');
        $valid = $request->get('valid');

        $query = SeoStructuredData::query();

        if ($type) {
            $query->where('type', $type);
        }

        if ($valid !== null) {
            $query->where('is_valid', $valid === 'true');
        }

        $schemas = $query->orderByDesc('updated_at')->get();

        // Get types for filter
        $types = SeoStructuredData::distinct()->pluck('type');

        return response()->json([
            'success' => true,
            'data' => $schemas,
            'meta' => [
                'total' => $schemas->count(),
                'valid' => $schemas->where('is_valid', true)->count(),
                'invalid' => $schemas->where('is_valid', false)->count(),
                'types' => $types,
            ]
        ]);
    }

    /**
     * Get content ideas
     */
    public function contentIdeas(Request $request)
    {
        $status = $request->get('status', 'pending');
        $priority = $request->get('priority');
        $source = $request->get('source');

        $query = SeoContentIdea::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($source) {
            $query->where('source', $source);
        }

        $ideas = $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderByDesc('estimated_traffic')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $ideas->items(),
            'meta' => [
                'total' => $ideas->total(),
                'pending' => SeoContentIdea::pending()->count(),
                'approved' => SeoContentIdea::approved()->count(),
            ],
            'pagination' => [
                'current_page' => $ideas->currentPage(),
                'last_page' => $ideas->lastPage(),
            ]
        ]);
    }

    /**
     * Approve a content idea
     */
    public function approveContentIdea($id)
    {
        $idea = SeoContentIdea::findOrFail($id);
        $idea->approve();

        return response()->json([
            'success' => true,
            'message' => 'Content idea approved',
        ]);
    }

    /**
     * Get SEO reports
     */
    public function reports(Request $request)
    {
        $type = $request->get('type', 'daily');
        $limit = $request->get('limit', 30);

        $reports = $this->reportingService->getReportHistory($type, $limit);

        // Get latest report of each type
        $latestDaily = SeoReport::getLatest('daily');
        $latestWeekly = SeoReport::getLatest('weekly');
        $latestMonthly = SeoReport::getLatest('monthly');

        return response()->json([
            'success' => true,
            'data' => $reports,
            'latest' => [
                'daily' => $latestDaily,
                'weekly' => $latestWeekly,
                'monthly' => $latestMonthly,
            ]
        ]);
    }

    /**
     * Get a specific report
     */
    public function getReport($id)
    {
        $report = SeoReport::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Get action logs
     */
    public function actionLogs(Request $request)
    {
        $type = $request->get('type');
        $date = $request->get('date');

        $query = SeoActionLog::query();

        if ($type) {
            $query->where('action_type', $type);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $logs = $query->orderByDesc('created_at')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'today' => SeoActionLog::today()->count(),
            ],
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
            ]
        ]);
    }

    /**
     * Sync all SEO data
     */
    public function syncAll(Request $request)
    {
        $geo = $request->get('geo', 'US');

        try {
            $results = $this->automationService->syncAllKeywords($geo);

            return response()->json([
                'success' => true,
                'message' => 'SEO data synchronized successfully',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run technical audit
     */
    public function runTechnicalAudit()
    {
        try {
            $results = $this->technicalService->runFullAudit();

            return response()->json([
                'success' => true,
                'message' => 'Technical audit completed',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Audit failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate content ideas
     */
    public function generateContentIdeas()
    {
        try {
            $ideas = $this->automationService->generateContentIdeas();

            return response()->json([
                'success' => true,
                'message' => 'Content ideas generated',
                'count' => count($ideas),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Generation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit URLs for reindexing
     */
    public function submitForReindex(Request $request)
    {
        $urls = $request->get('urls', []);
        $priority = $request->get('priority', 'normal');

        if (empty($urls)) {
            return response()->json([
                'success' => false,
                'message' => 'No URLs provided',
            ], 400);
        }

        try {
            $count = $this->technicalService->submitForReindex($urls, $priority);

            return response()->json([
                'success' => true,
                'message' => "{$count} URLs submitted for reindexing",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Submission failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update sitemap
     */
    public function updateSitemap()
    {
        try {
            $count = $this->technicalService->updateSitemap();

            return response()->json([
                'success' => true,
                'message' => "Sitemap updated with {$count} URLs",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sitemap update failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate report manually
     */
    public function generateReport(Request $request)
    {
        $type = $request->get('type', 'daily');

        try {
            switch ($type) {
                case 'daily':
                    $report = $this->reportingService->generateDailyReport();
                    break;
                case 'weekly':
                    $report = $this->reportingService->generateWeeklyReport();
                    break;
                case 'monthly':
                    $report = $this->reportingService->generateMonthlyReport();
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid report type',
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' report generated',
                'report_id' => $report->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get SERP analysis for a keyword
     */
    public function serpAnalysis(Request $request)
    {
        $keyword = $request->get('keyword');
        $geo = $request->get('geo', 'us');

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword is required',
            ], 400);
        }

        try {
            $results = $this->serpService->search($keyword, ['country' => $geo]);

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SERP analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get competitor analysis
     */
    public function competitorAnalysis(Request $request)
    {
        $competitors = $request->get('competitors', []);

        if (empty($competitors)) {
            $competitors = ['competitor1.com', 'competitor2.com'];
        }

        try {
            $gaps = $this->serpService->analyzeKeywordGap('smmjd.com', $competitors);

            return response()->json([
                'success' => true,
                'data' => [
                    'keyword_gaps' => $gaps,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Legacy endpoints for backwards compatibility

    public function performanceByCountry()
    {
        $data = $this->gscService->getPerformanceByCountry();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function performanceByDevice()
    {
        $data = $this->gscService->getPerformanceByDevice();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function performanceByPage()
    {
        $data = $this->gscService->getPerformanceByPage();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function trends(Request $request)
    {
        $geo = $request->get('geo', 'US');
        $keyword = $request->get('keyword');

        if ($keyword) {
            $data = $this->trendsService->fetchTrendsData($keyword, $geo);
        } else {
            $data = $this->trendsService->fetchAllTrends($geo);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function risingKeywords(Request $request)
    {
        $limit = $request->get('limit', 20);
        $keywords = $this->trendsService->getRisingKeywords($limit);
        return response()->json(['success' => true, 'data' => $keywords]);
    }

    public function syncGsc()
    {
        try {
            $data = $this->gscService->fetchSearchAnalytics();
            Cache::forget('gsc_summary_stats');

            return response()->json([
                'success' => true,
                'message' => 'GSC data synchronized',
                'keywords_synced' => count($data),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function syncTrends(Request $request)
    {
        try {
            $geo = $request->get('geo', 'US');
            $data = $this->trendsService->fetchAllTrends($geo);
            Cache::forget('trends_summary');

            return response()->json([
                'success' => true,
                'message' => 'Trends data synchronized',
                'keywords_synced' => count($data),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportKeywords(Request $request)
    {
        $source = $request->get('source', 'all');

        $query = SeoKeyword::active();

        if ($source !== 'all') {
            $query->where('source', $source);
        }

        $keywords = $query->orderByDesc('clicks')->get();

        $csv = "Keyword,Source,Intent,Cluster,Clicks,Impressions,CTR,Position,Search Volume,Rising,Declining,Opportunity\n";

        foreach ($keywords as $kw) {
            $csv .= "\"{$kw->keyword}\",{$kw->source},{$kw->intent},{$kw->cluster},{$kw->clicks},{$kw->impressions},{$kw->ctr},{$kw->position},{$kw->search_volume}," .
                    ($kw->is_rising ? 'Yes' : 'No') . ',' .
                    ($kw->is_declining ? 'Yes' : 'No') . ',' .
                    ($kw->is_opportunity ? 'Yes' : 'No') . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="seo_keywords_' . date('Y-m-d') . '.csv"');
    }
}
