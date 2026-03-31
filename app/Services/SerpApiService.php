<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SerpApiService
{
    protected $apiKey;
    protected $baseUrl = 'https://serpapi.com/search';

    public function __construct()
    {
        $this->apiKey = config('services.serpapi.key');
    }

    /**
     * Search Google and get SERP results
     */
    public function search($query, $options = [])
    {
        $cacheKey = 'serp_search_' . md5($query . json_encode($options));

        return Cache::remember($cacheKey, 3600, function () use ($query, $options) {
            try {
                if (!$this->apiKey) {
                    return $this->getMockSearchResults($query);
                }

                $params = array_merge([
                    'api_key' => $this->apiKey,
                    'q' => $query,
                    'engine' => 'google',
                    'google_domain' => 'google.com',
                    'gl' => $options['country'] ?? 'us',
                    'hl' => $options['language'] ?? 'en',
                    'num' => $options['num'] ?? 10,
                ], $options);

                $response = Http::timeout(30)->get($this->baseUrl, $params);

                if ($response->successful()) {
                    return $this->parseSearchResults($response->json());
                }

                Log::error('SERP API Error', ['response' => $response->body()]);
                return $this->getMockSearchResults($query);

            } catch (\Exception $e) {
                Log::error('SERP API Exception', ['error' => $e->getMessage()]);
                return $this->getMockSearchResults($query);
            }
        });
    }

    /**
     * Get related searches for a keyword
     */
    public function getRelatedSearches($query, $geo = 'us')
    {
        $results = $this->search($query, ['country' => $geo]);
        return $results['related_searches'] ?? [];
    }

    /**
     * Get "People Also Ask" questions
     */
    public function getPeopleAlsoAsk($query, $geo = 'us')
    {
        $results = $this->search($query, ['country' => $geo]);
        return $results['people_also_ask'] ?? [];
    }

    /**
     * Check if a URL has a featured snippet for a keyword
     */
    public function getFeaturedSnippet($query)
    {
        $results = $this->search($query);
        return $results['featured_snippet'] ?? null;
    }

    /**
     * Get competitor keywords
     */
    public function getCompetitorKeywords($domain, $limit = 100)
    {
        $cacheKey = 'serp_competitor_' . md5($domain);

        return Cache::remember($cacheKey, 86400, function () use ($domain, $limit) {
            // This would require a different API endpoint or multiple searches
            // For now, return mock data
            return $this->getMockCompetitorKeywords($domain, $limit);
        });
    }

    /**
     * Get keyword difficulty and search volume
     */
    public function getKeywordMetrics($keyword)
    {
        $cacheKey = 'serp_metrics_' . md5($keyword);

        return Cache::remember($cacheKey, 86400, function () use ($keyword) {
            // SERP API doesn't directly provide this, would need additional service
            return $this->getMockKeywordMetrics($keyword);
        });
    }

    /**
     * Parse search results from SERP API response
     */
    protected function parseSearchResults($data)
    {
        $results = [
            'organic_results' => [],
            'featured_snippet' => null,
            'people_also_ask' => [],
            'related_searches' => [],
            'serp_features' => [],
        ];

        // Organic results
        if (isset($data['organic_results'])) {
            foreach ($data['organic_results'] as $result) {
                $results['organic_results'][] = [
                    'position' => $result['position'] ?? null,
                    'title' => $result['title'] ?? null,
                    'link' => $result['link'] ?? null,
                    'snippet' => $result['snippet'] ?? null,
                    'domain' => parse_url($result['link'] ?? '', PHP_URL_HOST),
                ];
            }
        }

        // Featured snippet
        if (isset($data['answer_box'])) {
            $results['featured_snippet'] = [
                'type' => $data['answer_box']['type'] ?? 'unknown',
                'title' => $data['answer_box']['title'] ?? null,
                'snippet' => $data['answer_box']['snippet'] ?? $data['answer_box']['answer'] ?? null,
                'link' => $data['answer_box']['link'] ?? null,
            ];
            $results['serp_features'][] = 'featured_snippet';
        }

        // People also ask
        if (isset($data['related_questions'])) {
            foreach ($data['related_questions'] as $question) {
                $results['people_also_ask'][] = [
                    'question' => $question['question'] ?? null,
                    'snippet' => $question['snippet'] ?? null,
                    'link' => $question['link'] ?? null,
                ];
            }
            $results['serp_features'][] = 'people_also_ask';
        }

        // Related searches
        if (isset($data['related_searches'])) {
            foreach ($data['related_searches'] as $search) {
                $results['related_searches'][] = $search['query'] ?? null;
            }
        }

        // Detect other SERP features
        if (isset($data['knowledge_graph'])) {
            $results['serp_features'][] = 'knowledge_graph';
        }
        if (isset($data['local_results'])) {
            $results['serp_features'][] = 'local_pack';
        }
        if (isset($data['shopping_results'])) {
            $results['serp_features'][] = 'shopping';
        }
        if (isset($data['top_stories'])) {
            $results['serp_features'][] = 'top_stories';
        }
        if (isset($data['images_results'])) {
            $results['serp_features'][] = 'image_pack';
        }
        if (isset($data['videos_results'])) {
            $results['serp_features'][] = 'video_carousel';
        }

        return $results;
    }

    /**
     * Mock search results for testing/development
     */
    protected function getMockSearchResults($query)
    {
        $smmKeywords = [
            'buy instagram followers', 'smm panel', 'cheap followers',
            'tiktok followers', 'youtube subscribers', 'social media marketing'
        ];

        $isRelevant = false;
        foreach ($smmKeywords as $kw) {
            if (stripos($query, $kw) !== false || stripos($kw, $query) !== false) {
                $isRelevant = true;
                break;
            }
        }

        return [
            'organic_results' => [
                ['position' => 1, 'title' => 'Top SMM Panel - Best Prices', 'link' => 'https://example1.com', 'domain' => 'example1.com'],
                ['position' => 2, 'title' => 'Buy Instagram Followers Cheap', 'link' => 'https://example2.com', 'domain' => 'example2.com'],
                ['position' => 3, 'title' => 'SMM Panel Services', 'link' => 'https://smmjd.com', 'domain' => 'smmjd.com'],
            ],
            'featured_snippet' => $isRelevant ? [
                'type' => 'paragraph',
                'title' => 'What is an SMM Panel?',
                'snippet' => 'An SMM panel is a social media marketing panel that provides services like followers, likes, and views.',
            ] : null,
            'people_also_ask' => [
                ['question' => 'What is the cheapest SMM panel?', 'snippet' => 'The cheapest SMM panels offer services starting from $0.01 per 1000.'],
                ['question' => 'Are SMM panels safe?', 'snippet' => 'Reputable SMM panels are safe when they use organic methods.'],
                ['question' => 'How does SMM panel work?', 'snippet' => 'SMM panels work by connecting with API providers to deliver social media services.'],
            ],
            'related_searches' => [
                $query . ' cheap',
                $query . ' best',
                $query . ' reviews',
                'top ' . $query,
                $query . ' 2024',
            ],
            'serp_features' => $isRelevant ? ['featured_snippet', 'people_also_ask', 'related_searches'] : ['related_searches'],
        ];
    }

    /**
     * Mock competitor keywords
     */
    protected function getMockCompetitorKeywords($domain, $limit)
    {
        $keywords = [
            ['keyword' => 'buy instagram followers', 'position' => 3, 'search_volume' => 74000, 'cpc' => 1.20],
            ['keyword' => 'smm panel', 'position' => 5, 'search_volume' => 33000, 'cpc' => 0.85],
            ['keyword' => 'cheap smm panel', 'position' => 2, 'search_volume' => 12000, 'cpc' => 0.65],
            ['keyword' => 'buy tiktok followers', 'position' => 4, 'search_volume' => 45000, 'cpc' => 0.95],
            ['keyword' => 'buy youtube subscribers', 'position' => 6, 'search_volume' => 28000, 'cpc' => 1.10],
            ['keyword' => 'instagram likes buy', 'position' => 7, 'search_volume' => 22000, 'cpc' => 0.75],
            ['keyword' => 'social media panel', 'position' => 8, 'search_volume' => 8500, 'cpc' => 0.55],
            ['keyword' => 'best smm panel', 'position' => 4, 'search_volume' => 15000, 'cpc' => 0.90],
        ];

        return array_slice($keywords, 0, $limit);
    }

    /**
     * Mock keyword metrics
     */
    protected function getMockKeywordMetrics($keyword)
    {
        $baseVolume = strlen($keyword) * 500;
        $wordCount = str_word_count($keyword);

        return [
            'keyword' => $keyword,
            'search_volume' => rand($baseVolume, $baseVolume * 3),
            'cpc' => round(rand(50, 200) / 100, 2),
            'competition' => round(rand(30, 90) / 100, 2),
            'difficulty' => rand(20, 80),
            'trend' => rand(-20, 30),
            'is_long_tail' => $wordCount >= 4,
        ];
    }

    /**
     * Analyze keyword gap between site and competitors
     */
    public function analyzeKeywordGap($siteDomain, $competitorDomains)
    {
        $siteKeywords = $this->getCompetitorKeywords($siteDomain);
        $siteKeywordList = array_column($siteKeywords, 'keyword');

        $gaps = [];

        foreach ($competitorDomains as $competitor) {
            $competitorKeywords = $this->getCompetitorKeywords($competitor);

            foreach ($competitorKeywords as $kw) {
                if (!in_array($kw['keyword'], $siteKeywordList)) {
                    $gaps[] = [
                        'keyword' => $kw['keyword'],
                        'competitor' => $competitor,
                        'position' => $kw['position'],
                        'search_volume' => $kw['search_volume'],
                        'opportunity_score' => $this->calculateOpportunityScore($kw),
                    ];
                }
            }
        }

        // Sort by opportunity score
        usort($gaps, fn($a, $b) => $b['opportunity_score'] <=> $a['opportunity_score']);

        return array_slice($gaps, 0, 50);
    }

    /**
     * Calculate opportunity score for a keyword
     */
    protected function calculateOpportunityScore($keyword)
    {
        $volumeScore = min(100, ($keyword['search_volume'] ?? 0) / 1000);
        $positionScore = max(0, 100 - (($keyword['position'] ?? 10) * 10));
        $competitionScore = 100 - (($keyword['competition'] ?? 0.5) * 100);

        return round(($volumeScore * 0.4) + ($positionScore * 0.3) + ($competitionScore * 0.3));
    }
}
