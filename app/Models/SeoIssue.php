<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'severity',
        'url',
        'title',
        'description',
        'details',
        'status',
        'detected_at',
        'resolved_at',
    ];

    protected $casts = [
        'details' => 'array',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    const TYPES = [
        '404' => '404 Error',
        'redirect_loop' => 'Redirect Loop',
        'canonical' => 'Canonical Issue',
        'structured_data' => 'Structured Data Error',
        'core_web_vitals' => 'Core Web Vitals',
        'thin_content' => 'Thin Content',
        'duplicate_title' => 'Duplicate Title',
        'duplicate_description' => 'Duplicate Description',
        'missing_meta' => 'Missing Meta Description',
        'crawl_error' => 'Crawl Error',
        'index_issue' => 'Indexing Issue',
        'sitemap' => 'Sitemap Issue',
    ];

    const SEVERITIES = [
        'critical' => 'Critical',
        'warning' => 'Warning',
        'info' => 'Info',
    ];

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function resolve()
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function ignore()
    {
        $this->update(['status' => 'ignored']);
    }
}
