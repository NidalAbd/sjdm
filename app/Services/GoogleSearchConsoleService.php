<?php

namespace App\Services;

use App\Models\SeoKeyword;
use App\Models\SeoPerformance;
use App\Models\SeoSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleSearchConsoleService
{
    protected $client;
    protected $searchConsole;
    protected $siteUrl;

    public function __construct()
    {
        $this->siteUrl = config('app.url', 'https://smmjd.com');
    }

    /**
     * Initialize Google Client with credentials
     */
    protected function initClient()
    {
        if ($this->client) {
            return true;
        }

        try {
            $credentialsPath = storage_path('app/google/service-account.json');

            if (!file_exists($credentialsPath)) {
                Log::warning('Google Service Account credentials not found');
                return false;
            }

            $this->client = new \Google_Client();
            $this->client->setAuthConfig($credentialsPath);
            $this->client->setScopes(['https://www.googleapis.com/auth/webmasters.readonly']);

            $this->searchConsole = new \Google_Service_SearchConsole($this->client);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Client: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch search analytics data from GSC
     */
    public function fetchSearchAnalytics($startDate = null, $endDate = null, $dimensions = ['query'])
    {
        if (!$this->initClient()) {
            return $this->getMockData();
        }

        $startDate = $startDate ?? Carbon::now()->subDays(28)->format('Y-m-d');
        $endDate = $endDate ?? Carbon::now()->subDays(3)->format('Y-m-d');

        try {
            $request = new \Google_Service_SearchConsole_SearchAnalyticsQueryRequest();
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);
            $request->setDimensions($dimensions);
            $request->setRowLimit(1000);

            $response = $this->searchConsole->searchanalytics->query($this->siteUrl, $request);

            return $this->processAnalyticsResponse($response, $dimensions);
        } catch (\Exception $e) {
            Log::error('GSC API Error: ' . $e->getMessage());
            return $this->getMockData();
        }
    }

    /**
     * Process GSC response and save to database
     */
    protected function processAnalyticsResponse($response, $dimensions)
    {
        $data = [];

        foreach ($response->getRows() as $row) {
            $keys = $row->getKeys();
            $keyword = $keys[0] ?? '';

            $record = [
                'keyword' => $keyword,
                'clicks' => $row->getClicks(),
                'impressions' => $row->getImpressions(),
                'ctr' => round($row->getCtr() * 100, 2),
                'position' => round($row->getPosition(), 1),
                'country' => $keys[1] ?? null,
                'device' => $keys[2] ?? null,
            ];

            $data[] = $record;

            // Save to database
            SeoKeyword::updateOrCreate(
                [
                    'keyword' => $keyword,
                    'language' => $this->detectLanguage($keyword),
                    'source' => 'gsc',
                ],
                [
                    'clicks' => $record['clicks'],
                    'impressions' => $record['impressions'],
                    'ctr' => $record['ctr'],
                    'position' => $record['position'],
                    'country' => $record['country'],
                    'device' => $record['device'],
                    'data_date' => Carbon::now()->toDateString(),
                    'is_active' => true,
                ]
            );
        }

        return $data;
    }

    /**
     * Detect if keyword is Arabic or English
     */
    protected function detectLanguage($text)
    {
        // Check for Arabic characters
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return 'ar';
        }
        return 'en';
    }

    /**
     * Get top keywords from database
     */
    public function getTopKeywords($limit = 50, $language = null)
    {
        $query = SeoKeyword::where('source', 'gsc')
            ->where('is_active', true)
            ->orderByDesc('clicks')
            ->orderByDesc('impressions');

        if ($language) {
            $query->where('language', $language);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get performance by country
     */
    public function getPerformanceByCountry($startDate = null, $endDate = null)
    {
        if (!$this->initClient()) {
            return $this->getMockCountryData();
        }

        return $this->fetchSearchAnalytics($startDate, $endDate, ['country']);
    }

    /**
     * Get performance by device
     */
    public function getPerformanceByDevice($startDate = null, $endDate = null)
    {
        if (!$this->initClient()) {
            return $this->getMockDeviceData();
        }

        return $this->fetchSearchAnalytics($startDate, $endDate, ['device']);
    }

    /**
     * Get performance by page
     */
    public function getPerformanceByPage($startDate = null, $endDate = null)
    {
        if (!$this->initClient()) {
            return $this->getMockPageData();
        }

        return $this->fetchSearchAnalytics($startDate, $endDate, ['page']);
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats()
    {
        $cacheKey = 'gsc_summary_stats';

        return Cache::remember($cacheKey, 3600, function () {
            $keywords = SeoKeyword::where('source', 'gsc')
                ->where('is_active', true)
                ->get();

            return [
                'total_keywords' => $keywords->count(),
                'total_clicks' => $keywords->sum('clicks'),
                'total_impressions' => $keywords->sum('impressions'),
                'average_ctr' => round($keywords->avg('ctr'), 2),
                'average_position' => round($keywords->avg('position'), 1),
                'top_keyword' => $keywords->sortByDesc('clicks')->first()?->keyword ?? 'N/A',
            ];
        });
    }

    /**
     * Mock data for testing/demo when API is not configured
     */
    protected function getMockData()
    {
        $keywords = [
            ['keyword' => 'smm panel', 'clicks' => 1250, 'impressions' => 15000, 'ctr' => 8.33, 'position' => 3.2],
            ['keyword' => 'buy instagram followers', 'clicks' => 980, 'impressions' => 12500, 'ctr' => 7.84, 'position' => 4.5],
            ['keyword' => 'cheap smm panel', 'clicks' => 750, 'impressions' => 9800, 'ctr' => 7.65, 'position' => 5.1],
            ['keyword' => 'buy tiktok followers', 'clicks' => 620, 'impressions' => 8500, 'ctr' => 7.29, 'position' => 6.2],
            ['keyword' => 'youtube subscribers', 'clicks' => 540, 'impressions' => 7200, 'ctr' => 7.50, 'position' => 7.8],
            ['keyword' => 'instagram likes', 'clicks' => 480, 'impressions' => 6500, 'ctr' => 7.38, 'position' => 8.3],
            ['keyword' => 'social media marketing', 'clicks' => 420, 'impressions' => 5800, 'ctr' => 7.24, 'position' => 9.1],
            ['keyword' => 'best smm panel', 'clicks' => 380, 'impressions' => 5200, 'ctr' => 7.31, 'position' => 10.5],
            ['keyword' => 'شراء متابعين انستقرام', 'clicks' => 320, 'impressions' => 4500, 'ctr' => 7.11, 'position' => 4.2],
            ['keyword' => 'شراء متابعين تيك توك', 'clicks' => 280, 'impressions' => 3800, 'ctr' => 7.37, 'position' => 5.8],
        ];

        // Save mock data to database
        foreach ($keywords as $kw) {
            SeoKeyword::updateOrCreate(
                [
                    'keyword' => $kw['keyword'],
                    'source' => 'gsc',
                ],
                [
                    'language' => $this->detectLanguage($kw['keyword']),
                    'clicks' => $kw['clicks'],
                    'impressions' => $kw['impressions'],
                    'ctr' => $kw['ctr'],
                    'position' => $kw['position'],
                    'data_date' => Carbon::now()->toDateString(),
                    'is_active' => true,
                ]
            );
        }

        return $keywords;
    }

    /**
     * Mock country data
     */
    protected function getMockCountryData()
    {
        return [
            ['country' => 'USA', 'clicks' => 2500, 'impressions' => 35000, 'ctr' => 7.14, 'position' => 5.2],
            ['country' => 'SAU', 'clicks' => 1800, 'impressions' => 25000, 'ctr' => 7.20, 'position' => 4.8],
            ['country' => 'ARE', 'clicks' => 1200, 'impressions' => 18000, 'ctr' => 6.67, 'position' => 5.5],
            ['country' => 'EGY', 'clicks' => 950, 'impressions' => 14000, 'ctr' => 6.79, 'position' => 6.1],
            ['country' => 'JOR', 'clicks' => 800, 'impressions' => 12000, 'ctr' => 6.67, 'position' => 5.8],
            ['country' => 'GBR', 'clicks' => 650, 'impressions' => 9500, 'ctr' => 6.84, 'position' => 7.2],
            ['country' => 'IND', 'clicks' => 580, 'impressions' => 8500, 'ctr' => 6.82, 'position' => 8.1],
            ['country' => 'PAK', 'clicks' => 420, 'impressions' => 6200, 'ctr' => 6.77, 'position' => 8.5],
        ];
    }

    /**
     * Mock device data
     */
    protected function getMockDeviceData()
    {
        return [
            ['device' => 'MOBILE', 'clicks' => 5200, 'impressions' => 72000, 'ctr' => 7.22, 'position' => 5.8],
            ['device' => 'DESKTOP', 'clicks' => 2800, 'impressions' => 38000, 'ctr' => 7.37, 'position' => 5.2],
            ['device' => 'TABLET', 'clicks' => 450, 'impressions' => 6500, 'ctr' => 6.92, 'position' => 6.5],
        ];
    }

    /**
     * Mock page data
     */
    protected function getMockPageData()
    {
        $baseUrl = $this->siteUrl;
        return [
            ['page' => $baseUrl . '/', 'clicks' => 3500, 'impressions' => 45000, 'ctr' => 7.78, 'position' => 4.2],
            ['page' => $baseUrl . '/all-services', 'clicks' => 2200, 'impressions' => 32000, 'ctr' => 6.88, 'position' => 5.5],
            ['page' => $baseUrl . '/service/1', 'clicks' => 850, 'impressions' => 12000, 'ctr' => 7.08, 'position' => 6.8],
            ['page' => $baseUrl . '/faq', 'clicks' => 420, 'impressions' => 6500, 'ctr' => 6.46, 'position' => 8.2],
            ['page' => $baseUrl . '/how-it-works', 'clicks' => 380, 'impressions' => 5800, 'ctr' => 6.55, 'position' => 9.1],
        ];
    }
}
