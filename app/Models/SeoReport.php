<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'report_date',
        'keywords_data',
        'indexing_data',
        'technical_issues',
        'content_suggestions',
        'trend_analysis',
        'performance_summary',
    ];

    protected $casts = [
        'report_date' => 'date',
        'keywords_data' => 'array',
        'indexing_data' => 'array',
        'technical_issues' => 'array',
        'content_suggestions' => 'array',
        'trend_analysis' => 'array',
        'performance_summary' => 'array',
    ];

    const TYPES = [
        'daily' => 'Daily Report',
        'weekly' => 'Weekly Report',
        'monthly' => 'Monthly Report',
    ];

    public function scopeDaily($query)
    {
        return $query->where('type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('type', 'monthly');
    }

    public static function getLatest($type = 'daily')
    {
        return self::where('type', $type)
            ->orderByDesc('report_date')
            ->first();
    }
}
