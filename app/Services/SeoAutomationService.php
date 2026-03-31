<?php

namespace App\Services;

use App\Models\SeoKeyword;
use App\Models\SeoKeywordHistory;
use App\Models\SeoPage;
use App\Models\SeoIssue;
use App\Models\SeoContentIdea;
use App\Models\SeoActionLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeoAutomationService
{
    protected $gscService;
    protected $trendsService;
    protected $serpService;

    public function __construct(
        GoogleSearchConsoleService $gscService,
        GoogleTrendsService $trendsService,
        SerpApiService $serpService
    ) {
        $this->gscService = $gscService;
        $this->trendsService = $trendsService;
        $this->serpService = $serpService;
    }

    /**
     * Sync all keyword data from GSC, Trends, and SERP
     */
    public function syncAllKeywords($geo = 'US')
    {
        $results = [
            'gsc' => $this->syncGscKeywords(),
            'trends' => $this->syncTrendsKeywords($geo),
            'serp' => $this->enrichKeywordsWithSerp(),
        ];

        // Analyze and cluster keywords
        $this->clusterKeywords();
        $this->detectKeywordIntent();
        $this->identifyOpportunities();
        $this->detectDecliningKeywords();

        SeoActionLog::log('keyword_sync', 'Complete keyword sync completed', [
            'new_value' => $results,
        ]);

        return $results;
    }

    /**
     * Sync keywords from Google Search Console
     */
    public function syncGscKeywords()
    {
        try {
            $data = $this->gscService->fetchSearchAnalytics();
            $synced = 0;

            foreach ($data as $item) {
                $keyword = SeoKeyword::updateOrCreate(
                    ['keyword' => $item['keyword'], 'source' => 'gsc'],
                    [
                        'impressions' => $item['impressions'],
                        'clicks' => $item['clicks'],
                        'ctr' => $item['ctr'],
                        'previous_position' => SeoKeyword::where('keyword', $item['keyword'])
                            ->where('source', 'gsc')
                            ->value('position'),
                        'position' => $item['position'],
                        'geo' => $item['country'] ?? 'US',
                        'last_seen' => now(),
                        'status' => 'active',
                    ]
                );

                // Track history
                SeoKeywordHistory::create([
                    'keyword_id' => $keyword->id,
                    'impressions' => $item['impressions'],
                    'clicks' => $item['clicks'],
                    'ctr' => $item['ctr'],
                    'position' => $item['position'],
                    'date' => now()->toDateString(),
                ]);

                // Mark first seen if new
                if (!$keyword->first_seen) {
                    $keyword->update(['first_seen' => now()]);
                }

                // Detect if long-tail
                if (str_word_count($item['keyword']) >= 4) {
                    $keyword->update(['is_long_tail' => true]);
                }

                $synced++;
            }

            Log::info('GSC Keywords synced', ['count' => $synced]);
            return ['synced' => $synced];

        } catch (\Exception $e) {
            Log::error('GSC Keyword sync failed', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Sync keywords from Google Trends
     */
    public function syncTrendsKeywords($geo = 'US')
    {
        try {
            $data = $this->trendsService->fetchAllTrends($geo);
            $synced = 0;

            foreach ($data as $item) {
                SeoKeyword::updateOrCreate(
                    ['keyword' => $item['keyword'], 'source' => 'trends'],
                    [
                        'trend_score' => $item['interest_score'] ?? 0,
                        'is_rising' => $item['is_rising'] ?? false,
                        'geo' => $geo,
                        'related_keywords' => $item['related_topics'] ?? [],
                        'last_seen' => now(),
                        'status' => 'active',
                    ]
                );
                $synced++;
            }

            Log::info('Trends Keywords synced', ['count' => $synced]);
            return ['synced' => $synced];

        } catch (\Exception $e) {
            Log::error('Trends Keyword sync failed', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Enrich keywords with SERP data
     */
    public function enrichKeywordsWithSerp($limit = 50)
    {
        $keywords = SeoKeyword::active()
            ->whereNull('serp_features')
            ->orderByDesc('impressions')
            ->limit($limit)
            ->get();

        $enriched = 0;

        foreach ($keywords as $keyword) {
            try {
                $serpData = $this->serpService->search($keyword->keyword);
                $metrics = $this->serpService->getKeywordMetrics($keyword->keyword);

                $keyword->update([
                    'serp_features' => $serpData['serp_features'] ?? [],
                    'related_keywords' => array_merge(
                        $keyword->related_keywords ?? [],
                        $serpData['related_searches'] ?? []
                    ),
                    'search_volume' => $metrics['search_volume'] ?? null,
                    'competition' => $metrics['competition'] ?? null,
                    'cpc' => $metrics['cpc'] ?? null,
                ]);

                $enriched++;
            } catch (\Exception $e) {
                Log::warning('SERP enrichment failed for keyword', [
                    'keyword' => $keyword->keyword,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return ['enriched' => $enriched];
    }

    /**
     * Cluster similar keywords together
     */
    public function clusterKeywords()
    {
        $keywords = SeoKeyword::active()->whereNull('cluster')->get();

        $clusters = [
            'instagram' => ['instagram', 'ig', 'insta'],
            'tiktok' => ['tiktok', 'tik tok', 'tt'],
            'youtube' => ['youtube', 'yt', 'subscribers'],
            'facebook' => ['facebook', 'fb'],
            'twitter' => ['twitter', 'x.com', 'tweet'],
            'followers' => ['followers', 'follow'],
            'likes' => ['likes', 'like'],
            'views' => ['views', 'view', 'watch'],
            'comments' => ['comments', 'comment'],
            'shares' => ['shares', 'share', 'repost'],
            'smm_general' => ['smm', 'panel', 'social media marketing'],
            'pricing' => ['cheap', 'price', 'cost', 'affordable', 'discount'],
            'quality' => ['real', 'active', 'genuine', 'organic', 'best'],
        ];

        $clustered = 0;

        foreach ($keywords as $keyword) {
            $kw = strtolower($keyword->keyword);

            foreach ($clusters as $clusterName => $terms) {
                foreach ($terms as $term) {
                    if (strpos($kw, $term) !== false) {
                        $keyword->update(['cluster' => $clusterName]);
                        $clustered++;
                        break 2;
                    }
                }
            }
        }

        Log::info('Keywords clustered', ['count' => $clustered]);
        return $clustered;
    }

    /**
     * Detect keyword intent
     */
    public function detectKeywordIntent()
    {
        $keywords = SeoKeyword::active()->whereNull('intent')->get();
        $detected = 0;

        foreach ($keywords as $keyword) {
            $kw = strtolower($keyword->keyword);
            $intent = 'informational';

            // Transactional
            if (preg_match('/\b(buy|order|purchase|get|cheap|price|discount|deal|coupon|free)\b/', $kw)) {
                $intent = 'transactional';
            }
            // Commercial
            elseif (preg_match('/\b(best|top|review|compare|vs|versus|alternative|cheapest)\b/', $kw)) {
                $intent = 'commercial';
            }
            // Navigational
            elseif (preg_match('/\b(login|sign in|website|official|app|download)\b/', $kw)) {
                $intent = 'navigational';
            }

            $keyword->update(['intent' => $intent]);
            $detected++;
        }

        Log::info('Keyword intents detected', ['count' => $detected]);
        return $detected;
    }

    /**
     * Identify keyword opportunities
     */
    public function identifyOpportunities()
    {
        // Opportunity: High impressions, low CTR, position 5-20
        $opportunities = SeoKeyword::active()
            ->where('impressions', '>', 100)
            ->where('ctr', '<', 0.05)
            ->whereBetween('position', [5, 20])
            ->update(['is_opportunity' => true]);

        // Also mark low competition + decent volume as opportunities
        SeoKeyword::active()
            ->where('search_volume', '>', 1000)
            ->where('competition', '<', 0.4)
            ->whereNull('position')
            ->update(['is_opportunity' => true]);

        Log::info('Keyword opportunities identified', ['count' => $opportunities]);
        return $opportunities;
    }

    /**
     * Detect declining keywords
     */
    public function detectDecliningKeywords()
    {
        $declining = 0;

        $keywords = SeoKeyword::active()
            ->whereNotNull('previous_position')
            ->get();

        foreach ($keywords as $keyword) {
            // Position dropped by more than 5
            if ($keyword->position - $keyword->previous_position > 5) {
                $keyword->update([
                    'is_declining' => true,
                    'is_rising' => false,
                ]);
                $declining++;

                // Create issue for significant drops
                if ($keyword->position - $keyword->previous_position > 10) {
                    SeoIssue::updateOrCreate(
                        ['type' => 'keyword_drop', 'url' => $keyword->keyword],
                        [
                            'severity' => 'warning',
                            'title' => "Keyword position dropped: {$keyword->keyword}",
                            'description' => "Position changed from {$keyword->previous_position} to {$keyword->position}",
                            'details' => [
                                'keyword' => $keyword->keyword,
                                'old_position' => $keyword->previous_position,
                                'new_position' => $keyword->position,
                            ],
                            'status' => 'open',
                            'detected_at' => now(),
                        ]
                    );
                }
            }
            // Position improved by more than 3
            elseif ($keyword->previous_position - $keyword->position > 3) {
                $keyword->update([
                    'is_rising' => true,
                    'is_declining' => false,
                ]);
            }
        }

        Log::info('Declining keywords detected', ['count' => $declining]);
        return $declining;
    }

    /**
     * Suggest replacement keywords for weak ones
     */
    public function suggestReplacementKeywords()
    {
        $weakKeywords = SeoKeyword::active()
            ->declining()
            ->orWhere(function ($q) {
                $q->where('position', '>', 30)
                  ->where('impressions', '<', 50);
            })
            ->get();

        $suggestions = [];

        foreach ($weakKeywords as $keyword) {
            // Get related searches from SERP
            $related = $this->serpService->getRelatedSearches($keyword->keyword);

            // Find similar keywords that perform better
            $alternatives = SeoKeyword::active()
                ->where('cluster', $keyword->cluster)
                ->where('id', '!=', $keyword->id)
                ->where('position', '<', $keyword->position)
                ->orderBy('position')
                ->limit(3)
                ->get();

            if ($alternatives->count() > 0 || !empty($related)) {
                $suggestions[] = [
                    'weak_keyword' => $keyword->keyword,
                    'position' => $keyword->position,
                    'impressions' => $keyword->impressions,
                    'alternatives' => $alternatives->pluck('keyword')->toArray(),
                    'related_suggestions' => array_slice($related, 0, 5),
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Find missing long-tail keyword opportunities
     */
    public function findLongTailOpportunities()
    {
        $shortKeywords = SeoKeyword::active()
            ->whereRaw('LENGTH(keyword) - LENGTH(REPLACE(keyword, " ", "")) < 2')
            ->where('impressions', '>', 500)
            ->get();

        $longTailSuggestions = [];

        foreach ($shortKeywords as $keyword) {
            // Get people also ask questions
            $paa = $this->serpService->getPeopleAlsoAsk($keyword->keyword);

            // Get related searches
            $related = $this->serpService->getRelatedSearches($keyword->keyword);

            $longTailOptions = [];

            // Add PAA questions as long-tail opportunities
            foreach ($paa as $question) {
                if (isset($question['question']) && str_word_count($question['question']) >= 4) {
                    $longTailOptions[] = $question['question'];
                }
            }

            // Add related searches that are long-tail
            foreach ($related as $search) {
                if (str_word_count($search) >= 4) {
                    $longTailOptions[] = $search;
                }
            }

            if (!empty($longTailOptions)) {
                $longTailSuggestions[] = [
                    'seed_keyword' => $keyword->keyword,
                    'long_tail_options' => array_unique($longTailOptions),
                ];
            }
        }

        return $longTailSuggestions;
    }

    /**
     * Generate weekly content ideas based on trends
     */
    public function generateContentIdeas()
    {
        $ideas = [];

        // From rising keywords
        $risingKeywords = SeoKeyword::active()->rising()->limit(10)->get();
        foreach ($risingKeywords as $keyword) {
            SeoContentIdea::updateOrCreate(
                ['target_keyword' => $keyword->keyword],
                [
                    'title' => $this->generateContentTitle($keyword->keyword),
                    'description' => "Content targeting rising keyword: {$keyword->keyword}",
                    'related_keywords' => $keyword->related_keywords,
                    'content_type' => 'blog',
                    'priority' => 'high',
                    'source' => 'trends',
                    'estimated_traffic' => $keyword->search_volume ?? $keyword->impressions,
                ]
            );
            $ideas[] = $keyword->keyword;
        }

        // From opportunity keywords
        $opportunities = SeoKeyword::active()->opportunities()->limit(10)->get();
        foreach ($opportunities as $keyword) {
            SeoContentIdea::updateOrCreate(
                ['target_keyword' => $keyword->keyword],
                [
                    'title' => $this->generateContentTitle($keyword->keyword),
                    'description' => "Optimization opportunity for: {$keyword->keyword}",
                    'content_type' => 'landing_page',
                    'priority' => 'medium',
                    'source' => 'gap_analysis',
                    'estimated_traffic' => $keyword->search_volume ?? $keyword->impressions,
                ]
            );
            $ideas[] = $keyword->keyword;
        }

        // From competitor gap analysis
        $gaps = $this->serpService->analyzeKeywordGap('smmjd.com', ['competitor1.com', 'competitor2.com']);
        foreach (array_slice($gaps, 0, 5) as $gap) {
            SeoContentIdea::updateOrCreate(
                ['target_keyword' => $gap['keyword']],
                [
                    'title' => $this->generateContentTitle($gap['keyword']),
                    'description' => "Competitor keyword gap from {$gap['competitor']}",
                    'content_type' => 'service_page',
                    'priority' => 'high',
                    'source' => 'competitor',
                    'estimated_traffic' => $gap['search_volume'] ?? 0,
                ]
            );
            $ideas[] = $gap['keyword'];
        }

        SeoActionLog::log('content_suggestion', 'Generated weekly content ideas', [
            'new_value' => ['ideas_count' => count($ideas)],
        ]);

        return $ideas;
    }

    /**
     * Generate content title from keyword
     */
    protected function generateContentTitle($keyword)
    {
        $templates = [
            "How to {keyword}: Complete Guide 2024",
            "Best {keyword} - Expert Tips & Strategies",
            "{keyword}: Everything You Need to Know",
            "The Ultimate Guide to {keyword}",
            "{keyword} Made Simple: Step-by-Step",
        ];

        $template = $templates[array_rand($templates)];
        return str_replace('{keyword}', ucwords($keyword), $template);
    }

    /**
     * Get keyword analytics summary
     */
    public function getKeywordAnalytics()
    {
        return [
            'total_keywords' => SeoKeyword::active()->count(),
            'from_gsc' => SeoKeyword::active()->fromSource('gsc')->count(),
            'from_trends' => SeoKeyword::active()->fromSource('trends')->count(),
            'from_serp' => SeoKeyword::active()->fromSource('serp')->count(),
            'rising' => SeoKeyword::active()->rising()->count(),
            'declining' => SeoKeyword::active()->declining()->count(),
            'opportunities' => SeoKeyword::active()->opportunities()->count(),
            'long_tail' => SeoKeyword::active()->longTail()->count(),
            'by_intent' => [
                'transactional' => SeoKeyword::active()->byIntent('transactional')->count(),
                'commercial' => SeoKeyword::active()->byIntent('commercial')->count(),
                'informational' => SeoKeyword::active()->byIntent('informational')->count(),
                'navigational' => SeoKeyword::active()->byIntent('navigational')->count(),
            ],
            'clusters' => SeoKeyword::active()
                ->select('cluster', DB::raw('count(*) as count'))
                ->whereNotNull('cluster')
                ->groupBy('cluster')
                ->pluck('count', 'cluster')
                ->toArray(),
        ];
    }
}
