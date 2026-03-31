<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoTrend extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'related_keyword',
        'category',
        'interest_score',
        'timeframe',
        'geo',
        'is_rising',
        'rise_percentage',
        'data_date',
    ];

    protected $casts = [
        'is_rising' => 'boolean',
        'data_date' => 'date',
    ];

    /**
     * Scope for rising trends
     */
    public function scopeRising($query)
    {
        return $query->where('is_rising', true);
    }

    /**
     * Scope by geo/region
     */
    public function scopeForRegion($query, $geo)
    {
        return $query->where('geo', $geo);
    }

    /**
     * Get top trending keywords
     */
    public static function getTopTrending($limit = 20, $geo = null)
    {
        $query = self::orderByDesc('interest_score');

        if ($geo) {
            $query->forRegion($geo);
        }

        return $query->limit($limit)->get();
    }
}
