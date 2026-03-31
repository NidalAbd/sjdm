<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'keyword_ar',
        'source',
        'intent',
        'cluster',
        'impressions',
        'clicks',
        'ctr',
        'position',
        'previous_position',
        'search_volume',
        'competition',
        'cpc',
        'trend_score',
        'is_rising',
        'is_declining',
        'is_opportunity',
        'is_long_tail',
        'status',
        'geo',
        'related_keywords',
        'serp_features',
        'first_seen',
        'last_seen',
    ];

    protected $casts = [
        'related_keywords' => 'array',
        'serp_features' => 'array',
        'is_rising' => 'boolean',
        'is_declining' => 'boolean',
        'is_opportunity' => 'boolean',
        'is_long_tail' => 'boolean',
        'first_seen' => 'date',
        'last_seen' => 'date',
        'ctr' => 'decimal:4',
        'position' => 'decimal:2',
        'previous_position' => 'decimal:2',
        'competition' => 'decimal:2',
        'cpc' => 'decimal:2',
    ];

    public function history()
    {
        return $this->hasMany(SeoKeywordHistory::class, 'keyword_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOpportunities($query)
    {
        return $query->where('is_opportunity', true);
    }

    public function scopeRising($query)
    {
        return $query->where('is_rising', true);
    }

    public function scopeDeclining($query)
    {
        return $query->where('is_declining', true);
    }

    public function scopeFromSource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeLongTail($query)
    {
        return $query->where('is_long_tail', true);
    }

    public function scopeByIntent($query, $intent)
    {
        return $query->where('intent', $intent);
    }

    public function scopeInCluster($query, $cluster)
    {
        return $query->where('cluster', $cluster);
    }

    public function getPositionChangeAttribute()
    {
        if ($this->previous_position === null) {
            return null;
        }
        return $this->previous_position - $this->position;
    }

    public function isImproving()
    {
        return $this->position_change > 0;
    }

    public static function getTopKeywords($limit = 50, $geo = null)
    {
        $query = self::active()
            ->orderByDesc('clicks')
            ->orderByDesc('impressions');

        if ($geo) {
            $query->where('geo', $geo);
        }

        return $query->limit($limit)->get();
    }

    public static function getRisingKeywords($limit = 20)
    {
        return self::active()
            ->rising()
            ->orderByDesc('trend_score')
            ->limit($limit)
            ->get();
    }

    public static function getDecliningKeywords($limit = 20)
    {
        return self::active()
            ->declining()
            ->orderBy('position', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getOpportunityKeywords($limit = 30)
    {
        return self::active()
            ->opportunities()
            ->orderByDesc('search_volume')
            ->orderBy('competition')
            ->limit($limit)
            ->get();
    }
}
