<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoActionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_type',
        'url',
        'description',
        'old_value',
        'new_value',
        'status',
        'error_message',
        'triggered_by',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    const ACTION_TYPES = [
        'meta_update' => 'Meta Data Update',
        'sitemap_update' => 'Sitemap Update',
        'reindex_request' => 'Reindex Request',
        'schema_inject' => 'Schema Injection',
        'keyword_sync' => 'Keyword Sync',
        'issue_detected' => 'Issue Detected',
        'issue_resolved' => 'Issue Resolved',
        'content_suggestion' => 'Content Suggestion',
        'report_generated' => 'Report Generated',
    ];

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('action_type', $type);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public static function log($actionType, $description, $data = [])
    {
        return self::create([
            'action_type' => $actionType,
            'url' => $data['url'] ?? null,
            'description' => $description,
            'old_value' => $data['old_value'] ?? null,
            'new_value' => $data['new_value'] ?? null,
            'status' => $data['status'] ?? 'completed',
            'error_message' => $data['error_message'] ?? null,
            'triggered_by' => $data['triggered_by'] ?? 'system',
        ]);
    }
}
