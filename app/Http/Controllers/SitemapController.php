<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SitemapController extends Controller
{
    protected $cacheDuration = 86400; // 1 day
    protected $languages = ['en', 'ar'];

    /**
     * Generate the main sitemap index - UPDATED to exclude categories/platforms
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
            ]
            // REMOVED: sitemap-categories.xml and sitemap-platforms.xml
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
     * Generate the services sitemap - ONLY individual service pages
     */
    public function services()
    {
        // Get only active services with proper names
        $services = Cache::remember('sitemap_services', $this->cacheDuration, function () {
            return Service::select(['service_id', 'updated_at'])
                ->whereNotNull('name_en')
                ->whereNotNull('name_ar')
                ->where('name_en', '!=', '')
                ->where('name_ar', '!=', '')
                ->get();
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
     * DISABLED: Categories sitemap - return 404
     */
    public function categories()
    {
        abort(404, 'Categories sitemap disabled');
    }

    /**
     * DISABLED: Platforms sitemap - return 404
     */
    public function platforms()
    {
        abort(404, 'Platforms sitemap disabled');
    }

    /**
     * Generate robots.txt file - UPDATED to block categories/platforms
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
        $content .= "Disallow: /category/\n";           // Block English categories
        $content .= "Disallow: /platform/\n";          // Block English platforms
        $content .= "Disallow: /ar/category/\n";       // Block Arabic categories
        $content .= "Disallow: /ar/platform/\n";       // Block Arabic platforms
        $content .= "\n";
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
