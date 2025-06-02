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
     * Supported languages
     */
    protected $languages = ['en', 'ar'];

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
     */
    public function main()
    {
        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
                'alternates' => $this->getAlternateUrls('/')
            ],
            [
                'loc' => url('/all-services'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
                'alternates' => $this->getAlternateUrls('/all-services')
            ],
            [
                'loc' => url('/faq'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
                'alternates' => $this->getAlternateUrls('/faq')
            ],
            [
                'loc' => url('/about'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
                'alternates' => $this->getAlternateUrls('/about')
            ],
            [
                'loc' => url('/contact-us'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
                'alternates' => $this->getAlternateUrls('/contact-us')
            ],
            [
                'loc' => url('/privacy-policy'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'yearly',
                'priority' => '0.5',
                'alternates' => $this->getAlternateUrls('/privacy-policy')
            ],
            [
                'loc' => url('/terms-and-conditions'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'yearly',
                'priority' => '0.5',
                'alternates' => $this->getAlternateUrls('/terms-and-conditions')
            ],
            [
                'loc' => url('/how-it-works'),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
                'alternates' => $this->getAlternateUrls('/how-it-works')
            ]
        ];

        return response()->view('sitemaps.urls', [
            'urls' => $urls
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate the services sitemap
     */
    public function services()
    {
        // Get all services
        $services = Cache::remember('sitemap_services', $this->cacheDuration, function () {
            return Service::select(['service_id', 'updated_at'])->get();
        });

        $urls = [];

        foreach ($services as $service) {
            // Add canonical URL (English) with alternates
            $urls[] = [
                'loc' => url('/service/' . $service->service_id),
                'lastmod' => $service->updated_at ? $service->updated_at->toDateString() : Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
                'alternates' => $this->getAlternateUrls('/service/' . $service->service_id)
            ];
        }

        return response()->view('sitemaps.urls', [
            'urls' => $urls
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate the categories sitemap
     */
    public function categories()
    {
        $urls = [];

        // Get English categories
        $enCategories = Cache::remember('sitemap_categories_en', $this->cacheDuration, function () {
            return DB::table('services')
                ->select('category_en')
                ->distinct()
                ->whereNotNull('category_en')
                ->where('category_en', '!=', '')
                ->pluck('category_en');
        });

        foreach ($enCategories as $category) {
            $urls[] = [
                'loc' => url('/category/' . urlencode($category)),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'alternates' => $this->getAlternateUrls('/category/' . urlencode($category))
            ];
        }

        return response()->view('sitemaps.urls', [
            'urls' => $urls
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate the platforms sitemap
     */
    public function platforms()
    {
        $urls = [];

        // Get platforms from ServiceController
        $serviceController = new ServiceController();
        $platforms = $serviceController->platforms;

        foreach ($platforms as $platformKey => $platformNames) {
            if ($platformKey === 'all') continue; // Skip the "all" option

            $urls[] = [
                'loc' => url('/platform/' . $platformKey),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'alternates' => $this->getAlternateUrls('/platform/' . $platformKey)
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
        $content .= "Disallow: /api/\n\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Generate alternate URLs for all supported languages
     */
    protected function getAlternateUrls($path)
    {
        $alternates = [];

        foreach ($this->languages as $lang) {
            if ($lang === 'en') {
                $alternates[$lang] = url($path);
            } else {
                $alternates[$lang] = url('/' . $lang . $path);
            }
        }

        return $alternates;
    }
}
