<?php

namespace App\Services;

use App\Models\SeoKeyword;
use App\Models\SeoTrend;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleTrendsService
{
    protected $baseKeywords = [
        'smm panel',
        'buy instagram followers',
        'buy tiktok followers',
        'buy youtube subscribers',
        'social media marketing',
        'instagram likes',
        'tiktok views',
        'youtube views',
        'facebook likes',
        'twitter followers',
    ];

    protected $arabicKeywords = [
        'شراء متابعين انستقرام',
        'شراء متابعين تيك توك',
        'زيادة متابعين',
        'تسويق سوشيال ميديا',
    ];

    /**
     * Fetch trends data using SerpAPI (Google Trends alternative)
     * Note: For production, you should use SerpAPI or similar service
     */
    public function fetchTrendsData($keyword, $geo = 'US', $timeframe = 'today 12-m')
    {
        $cacheKey = "trends_{$keyword}_{$geo}_{$timeframe}";

        return Cache::remember($cacheKey, 86400, function () use ($keyword, $geo, $timeframe) {
            // In production, use SerpAPI or pytrends via a Python microservice
            // For now, return simulated trend data
            return $this->getSimulatedTrendData($keyword, $geo);
        });
    }

    /**
     * Get simulated trend data (replace with real API in production)
     */
    protected function getSimulatedTrendData($keyword, $geo)
    {
        $baseScore = rand(60, 95);
        $isRising = rand(0, 1) === 1;
        $risePercentage = $isRising ? rand(50, 500) : null;

        return [
            'keyword' => $keyword,
            'interest_score' => $baseScore,
            'is_rising' => $isRising,
            'rise_percentage' => $risePercentage,
            'geo' => $geo,
            'related_queries' => $this->getRelatedQueries($keyword),
            'interest_by_region' => $this->getInterestByRegion($keyword),
            'interest_over_time' => $this->getInterestOverTime($keyword),
        ];
    }

    /**
     * Get related queries for a keyword
     */
    protected function getRelatedQueries($keyword)
    {
        $relatedMap = [
            'smm panel' => [
                ['query' => 'best smm panel', 'score' => 100, 'rising' => true],
                ['query' => 'cheap smm panel', 'score' => 85, 'rising' => true],
                ['query' => 'smm panel paypal', 'score' => 70, 'rising' => false],
                ['query' => 'smm panel instagram', 'score' => 65, 'rising' => true],
                ['query' => 'instant smm panel', 'score' => 55, 'rising' => true],
            ],
            'buy instagram followers' => [
                ['query' => 'buy real instagram followers', 'score' => 100, 'rising' => true],
                ['query' => 'buy instagram followers cheap', 'score' => 90, 'rising' => true],
                ['query' => 'buy 1000 instagram followers', 'score' => 75, 'rising' => false],
                ['query' => 'buy active instagram followers', 'score' => 70, 'rising' => true],
            ],
            'buy tiktok followers' => [
                ['query' => 'buy tiktok followers cheap', 'score' => 100, 'rising' => true],
                ['query' => 'buy real tiktok followers', 'score' => 95, 'rising' => true],
                ['query' => 'tiktok followers free', 'score' => 80, 'rising' => false],
                ['query' => 'buy tiktok views', 'score' => 75, 'rising' => true],
            ],
        ];

        return $relatedMap[$keyword] ?? [
            ['query' => $keyword . ' cheap', 'score' => 85, 'rising' => true],
            ['query' => $keyword . ' best', 'score' => 80, 'rising' => true],
            ['query' => $keyword . ' instant', 'score' => 70, 'rising' => false],
        ];
    }

    /**
     * Get interest by region
     */
    protected function getInterestByRegion($keyword)
    {
        return [
            ['region' => 'United States', 'code' => 'US', 'score' => rand(70, 100)],
            ['region' => 'Saudi Arabia', 'code' => 'SA', 'score' => rand(75, 100)],
            ['region' => 'United Arab Emirates', 'code' => 'AE', 'score' => rand(65, 95)],
            ['region' => 'Egypt', 'code' => 'EG', 'score' => rand(60, 90)],
            ['region' => 'Jordan', 'code' => 'JO', 'score' => rand(55, 85)],
            ['region' => 'United Kingdom', 'code' => 'GB', 'score' => rand(50, 80)],
            ['region' => 'India', 'code' => 'IN', 'score' => rand(60, 90)],
            ['region' => 'Pakistan', 'code' => 'PK', 'score' => rand(55, 85)],
        ];
    }

    /**
     * Get interest over time (last 12 months)
     */
    protected function getInterestOverTime($keyword)
    {
        $data = [];
        $baseScore = rand(50, 70);

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $variation = rand(-15, 20);
            $score = max(0, min(100, $baseScore + $variation));

            $data[] = [
                'date' => $date->format('Y-m'),
                'score' => $score,
            ];

            // Trend upward slightly over time
            $baseScore = min(100, $baseScore + rand(0, 3));
        }

        return $data;
    }

    /**
     * Fetch and store all SMM-related trends
     */
    public function fetchAllTrends($geo = 'US')
    {
        $allTrends = [];
        $keywords = array_merge($this->baseKeywords, $this->arabicKeywords);

        foreach ($keywords as $keyword) {
            $trendData = $this->fetchTrendsData($keyword, $geo);
            $allTrends[] = $trendData;

            // Save to database
            SeoTrend::updateOrCreate(
                [
                    'keyword' => $keyword,
                    'geo' => $geo,
                    'data_date' => Carbon::now()->toDateString(),
                ],
                [
                    'interest_score' => $trendData['interest_score'],
                    'is_rising' => $trendData['is_rising'],
                    'rise_percentage' => $trendData['rise_percentage'],
                    'timeframe' => 'month',
                    'category' => 'smm',
                ]
            );

            // Save related queries as keywords
            foreach ($trendData['related_queries'] as $related) {
                SeoKeyword::updateOrCreate(
                    [
                        'keyword' => $related['query'],
                        'source' => 'trends',
                    ],
                    [
                        'language' => $this->detectLanguage($related['query']),
                        'is_trending' => $related['rising'],
                        'trend_score' => $related['score'],
                        'data_date' => Carbon::now()->toDateString(),
                        'is_active' => true,
                    ]
                );
            }
        }

        return $allTrends;
    }

    /**
     * Get rising keywords
     */
    public function getRisingKeywords($limit = 20)
    {
        return SeoKeyword::where('source', 'trends')
            ->where('is_trending', true)
            ->where('is_active', true)
            ->orderByDesc('trend_score')
            ->limit($limit)
            ->get();
    }

    /**
     * Get seasonal trends analysis
     */
    public function getSeasonalAnalysis()
    {
        $trends = SeoTrend::where('data_date', '>=', Carbon::now()->subMonths(12))
            ->orderBy('data_date')
            ->get()
            ->groupBy('keyword');

        $analysis = [];
        foreach ($trends as $keyword => $data) {
            $scores = $data->pluck('interest_score')->toArray();
            $analysis[$keyword] = [
                'average' => round(array_sum($scores) / count($scores), 1),
                'peak' => max($scores),
                'low' => min($scores),
                'trend' => end($scores) > $scores[0] ? 'rising' : 'declining',
            ];
        }

        return $analysis;
    }

    /**
     * Detect language
     */
    protected function detectLanguage($text)
    {
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return 'ar';
        }
        return 'en';
    }

    /**
     * Get trending topics summary
     */
    public function getTrendsSummary()
    {
        return Cache::remember('trends_summary', 3600, function () {
            $risingKeywords = $this->getRisingKeywords(10);
            $topTrends = SeoTrend::orderByDesc('interest_score')
                ->limit(10)
                ->get();

            return [
                'rising_keywords' => $risingKeywords,
                'top_trends' => $topTrends,
                'total_tracked' => SeoKeyword::where('source', 'trends')->count(),
                'rising_count' => SeoKeyword::where('source', 'trends')->where('is_trending', true)->count(),
            ];
        });
    }
}
