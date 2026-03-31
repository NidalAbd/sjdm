<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'title',
        'meta_description',
        'h1',
        'headings',
        'canonical',
        'index_status',
        'crawl_status',
        'word_count',
        'is_thin_content',
        'has_duplicate_title',
        'has_duplicate_description',
        'page_speed_mobile',
        'page_speed_desktop',
        'lcp',
        'fid',
        'cls',
        'impressions',
        'clicks',
        'structured_data_types',
        'structured_data_errors',
        'in_sitemap',
        'last_crawled',
        'last_indexed',
    ];

    protected $casts = [
        'headings' => 'array',
        'structured_data_types' => 'array',
        'structured_data_errors' => 'array',
        'is_thin_content' => 'boolean',
        'has_duplicate_title' => 'boolean',
        'has_duplicate_description' => 'boolean',
        'in_sitemap' => 'boolean',
        'last_crawled' => 'date',
        'last_indexed' => 'date',
        'page_speed_mobile' => 'decimal:2',
        'page_speed_desktop' => 'decimal:2',
        'lcp' => 'decimal:2',
        'fid' => 'decimal:2',
        'cls' => 'decimal:4',
    ];

    public function scopeIndexed($query)
    {
        return $query->where('index_status', 'indexed');
    }

    public function scopeNotIndexed($query)
    {
        return $query->where('index_status', '!=', 'indexed');
    }

    public function scopeThinContent($query)
    {
        return $query->where('is_thin_content', true);
    }

    public function scopeWithIssues($query)
    {
        return $query->where(function ($q) {
            $q->where('is_thin_content', true)
              ->orWhere('has_duplicate_title', true)
              ->orWhere('has_duplicate_description', true)
              ->orWhereNotNull('structured_data_errors');
        });
    }

    public function hasCoreWebVitalsIssues()
    {
        // LCP should be < 2.5s, FID < 100ms, CLS < 0.1
        return ($this->lcp && $this->lcp > 2.5) ||
               ($this->fid && $this->fid > 100) ||
               ($this->cls && $this->cls > 0.1);
    }
}
