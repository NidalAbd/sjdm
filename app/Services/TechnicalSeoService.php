<?php

namespace App\Services;

use App\Models\SeoPage;
use App\Models\SeoIssue;
use App\Models\SeoStructuredData;
use App\Models\SeoActionLog;
use App\Models\SeoReindexQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use DOMDocument;
use DOMXPath;

class TechnicalSeoService
{
    protected $siteUrl;
    protected $gscService;

    public function __construct(GoogleSearchConsoleService $gscService = null)
    {
        $this->siteUrl = config('app.url', 'https://smmjd.com');
        $this->gscService = $gscService;
    }

    /**
     * Run full technical SEO audit
     */
    public function runFullAudit()
    {
        $results = [
            'pages_crawled' => $this->crawlPages(),
            'index_issues' => $this->checkIndexStatus(),
            'canonical_issues' => $this->checkCanonicals(),
            'redirect_issues' => $this->checkRedirects(),
            'structured_data' => $this->validateStructuredData(),
            'core_web_vitals' => $this->checkCoreWebVitals(),
            'sitemap_health' => $this->validateSitemap(),
            'content_issues' => $this->detectContentIssues(),
        ];

        SeoActionLog::log('technical_audit', 'Full technical SEO audit completed', [
            'new_value' => $results,
        ]);

        return $results;
    }

    /**
     * Crawl and analyze pages
     */
    public function crawlPages($urls = null)
    {
        if (!$urls) {
            $urls = $this->getUrlsFromSitemap();
        }

        $crawled = 0;

        foreach ($urls as $url) {
            try {
                $pageData = $this->analyzePage($url);

                SeoPage::updateOrCreate(
                    ['url' => $url],
                    $pageData
                );

                $crawled++;
            } catch (\Exception $e) {
                Log::warning('Page crawl failed', ['url' => $url, 'error' => $e->getMessage()]);

                SeoIssue::create([
                    'type' => 'crawl_error',
                    'severity' => 'warning',
                    'url' => $url,
                    'title' => 'Page crawl failed',
                    'description' => $e->getMessage(),
                    'status' => 'open',
                    'detected_at' => now(),
                ]);
            }
        }

        return $crawled;
    }

    /**
     * Analyze a single page
     */
    public function analyzePage($url)
    {
        $response = Http::timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception("HTTP {$response->status()}");
        }

        $html = $response->body();

        // Suppress HTML parsing warnings
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($dom);
        libxml_clear_errors();

        // Extract meta tags
        $title = $this->extractMeta($xpath, 'title');
        $description = $this->extractMetaContent($xpath, 'description');
        $canonical = $this->extractCanonical($xpath);

        // Extract headings
        $h1 = $this->extractTextContent($xpath, '//h1');
        $headings = $this->extractHeadings($xpath);

        // Count words (approximate)
        $textContent = strip_tags($html);
        $wordCount = str_word_count($textContent);

        // Check for structured data
        $structuredData = $this->extractStructuredData($html);

        return [
            'title' => $title,
            'meta_description' => $description,
            'h1' => $h1,
            'headings' => $headings,
            'canonical' => $canonical,
            'word_count' => $wordCount,
            'is_thin_content' => $wordCount < 300,
            'structured_data_types' => array_column($structuredData, '@type'),
            'last_crawled' => now(),
            'in_sitemap' => true,
        ];
    }

    /**
     * Check index status of pages
     */
    public function checkIndexStatus()
    {
        $issues = [];

        // Get index status from GSC if available
        $pages = SeoPage::all();

        foreach ($pages as $page) {
            // Simulate index status check (would use GSC URL Inspection API in production)
            $status = $this->simulateIndexCheck($page->url);

            $page->update([
                'index_status' => $status['status'],
                'crawl_status' => $status['crawl_status'],
            ]);

            if ($status['status'] !== 'indexed') {
                SeoIssue::updateOrCreate(
                    ['type' => 'index_issue', 'url' => $page->url],
                    [
                        'severity' => $status['status'] === 'crawled_not_indexed' ? 'warning' : 'critical',
                        'title' => "Page not indexed: {$status['status']}",
                        'description' => "URL {$page->url} has index status: {$status['status']}",
                        'details' => $status,
                        'status' => 'open',
                        'detected_at' => now(),
                    ]
                );
                $issues[] = $page->url;
            }
        }

        return count($issues);
    }

    /**
     * Check canonical issues
     */
    public function checkCanonicals()
    {
        $issues = [];

        $pages = SeoPage::whereNotNull('canonical')->get();

        foreach ($pages as $page) {
            // Check if canonical points to self
            if ($page->canonical !== $page->url) {
                // Check if canonical URL exists
                if (!SeoPage::where('url', $page->canonical)->exists()) {
                    SeoIssue::updateOrCreate(
                        ['type' => 'canonical', 'url' => $page->url],
                        [
                            'severity' => 'warning',
                            'title' => 'Broken canonical URL',
                            'description' => "Canonical URL {$page->canonical} not found",
                            'details' => ['canonical' => $page->canonical],
                            'status' => 'open',
                            'detected_at' => now(),
                        ]
                    );
                    $issues[] = $page->url;
                }
            }
        }

        // Check for missing canonicals
        $missingCanonicals = SeoPage::whereNull('canonical')->count();
        if ($missingCanonicals > 0) {
            SeoIssue::updateOrCreate(
                ['type' => 'canonical', 'url' => 'site-wide'],
                [
                    'severity' => 'info',
                    'title' => 'Missing canonical tags',
                    'description' => "{$missingCanonicals} pages missing canonical tags",
                    'status' => 'open',
                    'detected_at' => now(),
                ]
            );
        }

        return count($issues);
    }

    /**
     * Check for redirect loops and 404s
     */
    public function checkRedirects()
    {
        $issues = [];
        $urls = $this->getUrlsFromSitemap();

        foreach ($urls as $url) {
            try {
                $response = Http::withOptions([
                    'allow_redirects' => ['max' => 10, 'track_redirects' => true],
                ])->timeout(15)->get($url);

                $redirects = $response->header('X-Guzzle-Redirect-History');
                $statusCode = $response->status();

                if ($statusCode === 404) {
                    SeoIssue::updateOrCreate(
                        ['type' => '404', 'url' => $url],
                        [
                            'severity' => 'critical',
                            'title' => '404 Error',
                            'description' => "Page returns 404 error",
                            'status' => 'open',
                            'detected_at' => now(),
                        ]
                    );
                    $issues[] = ['url' => $url, 'type' => '404'];
                }

                // Check for redirect chains (more than 2 redirects)
                if ($redirects && count(explode(',', $redirects)) > 2) {
                    SeoIssue::updateOrCreate(
                        ['type' => 'redirect_loop', 'url' => $url],
                        [
                            'severity' => 'warning',
                            'title' => 'Redirect chain detected',
                            'description' => 'URL has more than 2 redirects',
                            'details' => ['redirects' => $redirects],
                            'status' => 'open',
                            'detected_at' => now(),
                        ]
                    );
                    $issues[] = ['url' => $url, 'type' => 'redirect_chain'];
                }

            } catch (\Exception $e) {
                Log::warning('Redirect check failed', ['url' => $url, 'error' => $e->getMessage()]);
            }
        }

        return count($issues);
    }

    /**
     * Validate structured data on all pages
     */
    public function validateStructuredData()
    {
        $results = [
            'valid' => 0,
            'errors' => 0,
            'warnings' => 0,
        ];

        $pages = SeoPage::whereNotNull('structured_data_types')->get();

        foreach ($pages as $page) {
            try {
                $response = Http::timeout(30)->get($page->url);
                $html = $response->body();
                $schemas = $this->extractStructuredData($html);

                foreach ($schemas as $schema) {
                    $validation = $this->validateSchema($schema);

                    SeoStructuredData::updateOrCreate(
                        ['url' => $page->url, 'type' => $schema['@type'] ?? 'Unknown'],
                        [
                            'schema_data' => $schema,
                            'is_valid' => $validation['is_valid'],
                            'errors' => $validation['errors'],
                            'warnings' => $validation['warnings'],
                        ]
                    );

                    if ($validation['is_valid']) {
                        $results['valid']++;
                    }
                    if (!empty($validation['errors'])) {
                        $results['errors']++;

                        SeoIssue::updateOrCreate(
                            ['type' => 'structured_data', 'url' => $page->url],
                            [
                                'severity' => 'warning',
                                'title' => 'Structured data errors',
                                'description' => implode(', ', $validation['errors']),
                                'details' => $validation,
                                'status' => 'open',
                                'detected_at' => now(),
                            ]
                        );
                    }
                    if (!empty($validation['warnings'])) {
                        $results['warnings']++;
                    }
                }

            } catch (\Exception $e) {
                Log::warning('Structured data validation failed', ['url' => $page->url, 'error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * Check Core Web Vitals
     */
    public function checkCoreWebVitals()
    {
        $issues = [];
        $pages = SeoPage::limit(20)->get(); // Check top 20 pages

        foreach ($pages as $page) {
            // In production, use PageSpeed Insights API
            $vitals = $this->getMockCoreWebVitals($page->url);

            $page->update([
                'lcp' => $vitals['lcp'],
                'fid' => $vitals['fid'],
                'cls' => $vitals['cls'],
                'page_speed_mobile' => $vitals['mobile_score'],
                'page_speed_desktop' => $vitals['desktop_score'],
            ]);

            // Check for issues
            if ($vitals['lcp'] > 2.5) {
                SeoIssue::updateOrCreate(
                    ['type' => 'core_web_vitals', 'url' => $page->url . '_lcp'],
                    [
                        'severity' => $vitals['lcp'] > 4 ? 'critical' : 'warning',
                        'url' => $page->url,
                        'title' => 'LCP too slow',
                        'description' => "LCP is {$vitals['lcp']}s (should be < 2.5s)",
                        'details' => ['lcp' => $vitals['lcp']],
                        'status' => 'open',
                        'detected_at' => now(),
                    ]
                );
                $issues[] = $page->url;
            }

            if ($vitals['cls'] > 0.1) {
                SeoIssue::updateOrCreate(
                    ['type' => 'core_web_vitals', 'url' => $page->url . '_cls'],
                    [
                        'severity' => $vitals['cls'] > 0.25 ? 'critical' : 'warning',
                        'url' => $page->url,
                        'title' => 'CLS too high',
                        'description' => "CLS is {$vitals['cls']} (should be < 0.1)",
                        'details' => ['cls' => $vitals['cls']],
                        'status' => 'open',
                        'detected_at' => now(),
                    ]
                );
            }
        }

        return count($issues);
    }

    /**
     * Validate sitemap health
     */
    public function validateSitemap()
    {
        $sitemapPath = public_path('sitemap.xml');
        $results = [
            'valid' => true,
            'url_count' => 0,
            'issues' => [],
        ];

        if (!File::exists($sitemapPath)) {
            $results['valid'] = false;
            $results['issues'][] = 'Sitemap.xml not found';

            SeoIssue::updateOrCreate(
                ['type' => 'sitemap', 'url' => 'sitemap.xml'],
                [
                    'severity' => 'critical',
                    'title' => 'Sitemap missing',
                    'description' => 'sitemap.xml file not found',
                    'status' => 'open',
                    'detected_at' => now(),
                ]
            );

            return $results;
        }

        try {
            $content = File::get($sitemapPath);
            $xml = simplexml_load_string($content);

            if ($xml === false) {
                $results['valid'] = false;
                $results['issues'][] = 'Invalid XML format';
                return $results;
            }

            $urls = [];
            foreach ($xml->url as $url) {
                $urls[] = (string)$url->loc;
            }

            $results['url_count'] = count($urls);

            // Check for duplicate URLs
            $duplicates = array_diff_assoc($urls, array_unique($urls));
            if (!empty($duplicates)) {
                $results['issues'][] = 'Duplicate URLs found: ' . count($duplicates);
            }

            // Check for 404s in sitemap
            foreach (array_slice($urls, 0, 50) as $url) { // Check first 50
                try {
                    $response = Http::timeout(10)->head($url);
                    if ($response->status() === 404) {
                        $results['issues'][] = "404 in sitemap: {$url}";
                    }
                } catch (\Exception $e) {
                    // Ignore timeout errors
                }
            }

        } catch (\Exception $e) {
            $results['valid'] = false;
            $results['issues'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Detect content issues (thin, duplicate)
     */
    public function detectContentIssues()
    {
        $issues = [
            'thin_content' => 0,
            'duplicate_titles' => 0,
            'duplicate_descriptions' => 0,
            'missing_meta' => 0,
        ];

        // Thin content
        $thinPages = SeoPage::where('word_count', '<', 300)->get();
        foreach ($thinPages as $page) {
            $page->update(['is_thin_content' => true]);

            SeoIssue::updateOrCreate(
                ['type' => 'thin_content', 'url' => $page->url],
                [
                    'severity' => 'warning',
                    'title' => 'Thin content',
                    'description' => "Page has only {$page->word_count} words",
                    'status' => 'open',
                    'detected_at' => now(),
                ]
            );
        }
        $issues['thin_content'] = $thinPages->count();

        // Duplicate titles
        $duplicateTitles = SeoPage::select('title')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('title')
            ->groupBy('title')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicateTitles as $dup) {
            SeoPage::where('title', $dup->title)->update(['has_duplicate_title' => true]);

            SeoIssue::updateOrCreate(
                ['type' => 'duplicate_title', 'url' => "title:{$dup->title}"],
                [
                    'severity' => 'warning',
                    'title' => 'Duplicate title',
                    'description' => "Title '{$dup->title}' used on {$dup->count} pages",
                    'status' => 'open',
                    'detected_at' => now(),
                ]
            );
        }
        $issues['duplicate_titles'] = $duplicateTitles->count();

        // Duplicate descriptions
        $duplicateDesc = SeoPage::select('meta_description')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('meta_description')
            ->groupBy('meta_description')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicateDesc as $dup) {
            SeoPage::where('meta_description', $dup->meta_description)
                ->update(['has_duplicate_description' => true]);
        }
        $issues['duplicate_descriptions'] = $duplicateDesc->count();

        // Missing meta descriptions
        $missingMeta = SeoPage::whereNull('meta_description')
            ->orWhere('meta_description', '')
            ->count();
        $issues['missing_meta'] = $missingMeta;

        if ($missingMeta > 0) {
            SeoIssue::updateOrCreate(
                ['type' => 'missing_meta', 'url' => 'site-wide'],
                [
                    'severity' => 'warning',
                    'title' => 'Missing meta descriptions',
                    'description' => "{$missingMeta} pages missing meta descriptions",
                    'status' => 'open',
                    'detected_at' => now(),
                ]
            );
        }

        return $issues;
    }

    /**
     * Submit URLs for reindexing
     */
    public function submitForReindex($urls, $priority = 'normal')
    {
        $submitted = 0;

        foreach ((array)$urls as $url) {
            SeoReindexQueue::queueUrl($url, 'URL_UPDATED', $priority);
            $submitted++;
        }

        // Process queue (in production, would call GSC Indexing API)
        $this->processReindexQueue();

        return $submitted;
    }

    /**
     * Process the reindex queue
     */
    public function processReindexQueue()
    {
        $pending = SeoReindexQueue::pending()
            ->orderByRaw("FIELD(priority, 'high', 'normal', 'low')")
            ->limit(100) // API quota limit
            ->get();

        foreach ($pending as $item) {
            // In production, call GSC Indexing API
            // For now, simulate
            $item->markSubmitted('Simulated submission');

            SeoActionLog::log('reindex_request', "Submitted for reindex: {$item->url}", [
                'url' => $item->url,
            ]);
        }

        return $pending->count();
    }

    /**
     * Update sitemap with new URLs
     */
    public function updateSitemap($urls = null)
    {
        $sitemapPath = public_path('sitemap.xml');

        if (!$urls) {
            // Get all active pages
            $urls = SeoPage::where('index_status', 'indexed')
                ->orWhereNull('index_status')
                ->pluck('url')
                ->toArray();
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= "    <loc>{$url}</loc>" . PHP_EOL;
            $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.8</priority>' . PHP_EOL;
            $xml .= '    <lastmod>' . now()->toDateString() . '</lastmod>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        File::put($sitemapPath, $xml);

        SeoActionLog::log('sitemap_update', 'Sitemap updated', [
            'new_value' => ['url_count' => count($urls)],
        ]);

        return count($urls);
    }

    // Helper methods

    protected function getUrlsFromSitemap()
    {
        $sitemapPath = public_path('sitemap.xml');
        $urls = [];

        if (File::exists($sitemapPath)) {
            $content = File::get($sitemapPath);
            $xml = @simplexml_load_string($content);

            if ($xml) {
                foreach ($xml->url as $url) {
                    $urls[] = (string)$url->loc;
                }
            }
        }

        return $urls;
    }

    protected function extractMeta($xpath, $tag)
    {
        $nodes = $xpath->query("//{$tag}");
        return $nodes->length > 0 ? $nodes->item(0)->textContent : null;
    }

    protected function extractMetaContent($xpath, $name)
    {
        $nodes = $xpath->query("//meta[@name='{$name}']/@content");
        return $nodes->length > 0 ? $nodes->item(0)->textContent : null;
    }

    protected function extractCanonical($xpath)
    {
        $nodes = $xpath->query("//link[@rel='canonical']/@href");
        return $nodes->length > 0 ? $nodes->item(0)->textContent : null;
    }

    protected function extractTextContent($xpath, $query)
    {
        $nodes = $xpath->query($query);
        return $nodes->length > 0 ? trim($nodes->item(0)->textContent) : null;
    }

    protected function extractHeadings($xpath)
    {
        $headings = ['h2' => [], 'h3' => []];

        foreach (['h2', 'h3'] as $tag) {
            $nodes = $xpath->query("//{$tag}");
            foreach ($nodes as $node) {
                $headings[$tag][] = trim($node->textContent);
            }
        }

        return $headings;
    }

    protected function extractStructuredData($html)
    {
        $schemas = [];

        preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $matches);

        foreach ($matches[1] as $json) {
            $data = json_decode($json, true);
            if ($data) {
                if (isset($data['@graph'])) {
                    $schemas = array_merge($schemas, $data['@graph']);
                } else {
                    $schemas[] = $data;
                }
            }
        }

        return $schemas;
    }

    protected function validateSchema($schema)
    {
        $result = [
            'is_valid' => true,
            'errors' => [],
            'warnings' => [],
        ];

        $type = $schema['@type'] ?? null;

        if (!$type) {
            $result['errors'][] = 'Missing @type';
            $result['is_valid'] = false;
            return $result;
        }

        // Type-specific validation
        switch ($type) {
            case 'Product':
                if (!isset($schema['name'])) $result['errors'][] = 'Product missing name';
                if (!isset($schema['offers'])) $result['warnings'][] = 'Product missing offers';
                break;

            case 'Article':
                if (!isset($schema['headline'])) $result['errors'][] = 'Article missing headline';
                if (!isset($schema['author'])) $result['warnings'][] = 'Article missing author';
                break;

            case 'BreadcrumbList':
                if (!isset($schema['itemListElement'])) $result['errors'][] = 'BreadcrumbList missing items';
                break;

            case 'FAQ':
            case 'FAQPage':
                if (!isset($schema['mainEntity'])) $result['errors'][] = 'FAQ missing mainEntity';
                break;
        }

        $result['is_valid'] = empty($result['errors']);
        return $result;
    }

    protected function simulateIndexCheck($url)
    {
        // Simulate index status (in production, use GSC URL Inspection API)
        $statuses = ['indexed', 'indexed', 'indexed', 'crawled_not_indexed', 'not_indexed'];
        return [
            'status' => $statuses[array_rand($statuses)],
            'crawl_status' => 'completed',
            'last_crawl' => now()->subDays(rand(1, 7)),
        ];
    }

    protected function getMockCoreWebVitals($url)
    {
        return [
            'lcp' => round(rand(15, 35) / 10, 1),
            'fid' => rand(20, 150),
            'cls' => round(rand(1, 20) / 100, 2),
            'mobile_score' => rand(60, 95),
            'desktop_score' => rand(75, 98),
        ];
    }
}
