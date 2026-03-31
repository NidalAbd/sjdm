<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SeoPerformance extends Model
{
    use HasFactory;

    protected $table = 'seo_performance';

    protected $fillable = [
        'date',
        'page_url',
        'country',
        'device',
        'impressions',
        'clicks',
        'ctr',
        'position',
    ];

    protected $casts = [
        'date' => 'date',
        'ctr' => 'decimal:4',
        'position' => 'decimal:2',
    ];

    /**
     * Get performance for a specific page
     */
    public static function getPagePerformance($pageUrl, $days = 30)
    {
        return self::where('page_url', $pageUrl)
            ->where('date', '>=', Carbon::now()->subDays($days))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get overall site performance
     */
    public static function getSitePerformance($days = 30)
    {
        return self::where('date', '>=', Carbon::now()->subDays($days))
            ->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, AVG(ctr) as ctr, AVG(position) as position')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top performing pages
     */
    public static function getTopPages($limit = 10, $days = 30)
    {
        return self::where('date', '>=', Carbon::now()->subDays($days))
            ->selectRaw('page_url, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(ctr) as avg_ctr, AVG(position) as avg_position')
            ->groupBy('page_url')
            ->orderByDesc('total_clicks')
            ->limit($limit)
            ->get();
    }
}
