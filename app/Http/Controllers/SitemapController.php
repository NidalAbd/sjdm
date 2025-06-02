<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Cache duration in seconds (1 day)
     */
    protected $cacheDuration = 86400;

    /**
     * Generate the main sitemap index
     */
    public function index()
    {
        $sitemaps = [
            [
                'url' => url('sitemap-main.xml'),
                'lastmod' => Carbon::now()->toDateString()
            ],
            [
                'url' => url('sitemap-services.xml'),
                'lastmod' => Carbon::now()->toDateString()
            ],
            [
                'url' => url('sitemap-categories.xml'),
                'lastmod' => Carbon::now()->toDateString()
            ],
            [
                'url' => url('sitemap-platforms.xml'),
                'lastmod' => Carbon::now()->toDateString()
            ]
        ];

        return response()->view('sitemaps.index', [
            'sitemaps' => $sitemaps
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate the main pages sitemap
     * ONLY canonical URLs (English versions)
     */
    public function main()
    {
        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'loc' => url('/all-services'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'loc' => url('/faq'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => url('/about'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => url('/contact-us'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => url('/privacy-policy'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'yearly',
                'priority' => '0.5'
            ],
            [
                'loc' => url('/terms-and-conditions'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'yearly',
                'priority' => '0.5'
            ],
            [
                'loc' => url('/how-it-works'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ]
        ];

        return response()->view('sitemaps.urls', [
            'urls' => $urls
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate the services sitemap
     * ONLY canonical service URLs (English versions)
     */
    public function services()
    {
        // Get all services
        $services = Cache::remember('sitemap_services', $this->cacheDuration, function () {
            return Service::select(['service_id', 'updated_at'])->get();
        });

        $urls = [];

        foreach ($services as $service) {
            // ONLY add canonical URLs (no Arabic versions)
            $urls[] = [
                'loc' => url('/service/' . $service->service_id),
                'lastmod' => $service->updated_at ? $service->updated_at->toDateString() : Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }

        return response()->view('sitemaps.urls', [
            'urls' => $urls
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate the categories sitemap
     * ONLY canonical category URLs (English versions)
     */
    public function categories()
    {
        $urls = [];

        // Get English categories only
        $enCategories = Cache::remember('sitemap_categories_en', $this->cacheDuration, function () {
            return DB::table('services')
                ->select('category_en')
                ->distinct()
                ->whereNotNull('category_en')
                ->where('category_en', '!=', '')
                ->pluck('category_en');
        });

        foreach ($enCategories as $category) {
            // ONLY add canonical URLs (no Arabic versions)
            $urls[] = [
                'loc' => url('/category/' . urlencode($category)),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        return response()->view('sitemaps.urls', [
            'urls' => $urls
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate the platforms sitemap
     * ONLY canonical platform URLs (English versions)
     */
    public function platforms()
    {
        $urls = [];

        // Get platforms from ServiceController
        $serviceController = new ServiceController();
        $platforms = $serviceController->platforms;

        foreach ($platforms as $platformKey => $platformNames) {
            if ($platformKey === 'all') continue; // Skip the "all" option

            // ONLY add canonical URLs (no Arabic versions)
            $urls[] = [
                'loc' => url('/platform/' . $platformKey),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        return response()->view('sitemaps.urls', [
            'urls' => $urls
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate robots.txt file
     */
    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /dashboard/\n";
        $content .= "Disallow: /user/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /register\n";
        $content .= "Disallow: /api/\n";
        $content .= "Disallow: /ar/\n";  // Block all Arabic URLs from crawling
        $content .= "\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content)->header('Content-Type', 'text/plain');
    }

    /**
     * Convert text to slug (if needed)
     */
    protected function slugify($text)
    {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim
        $text = trim($text, '-');

        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // Lowercase
        $text = strtolower($text);

        return $text ?: 'n-a';
    }
}
