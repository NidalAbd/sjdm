<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoReindexQueue extends Model
{
    use HasFactory;

    protected $table = 'seo_reindex_queue';

    protected $fillable = [
        'url',
        'type',
        'priority',
        'status',
        'response',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    const TYPES = [
        'URL_UPDATED' => 'URL Updated',
        'URL_DELETED' => 'URL Deleted',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public static function queueUrl($url, $type = 'URL_UPDATED', $priority = 'normal')
    {
        return self::updateOrCreate(
            ['url' => $url],
            [
                'type' => $type,
                'priority' => $priority,
                'status' => 'pending',
            ]
        );
    }

    public function markSubmitted($response = null)
    {
        $this->update([
            'status' => 'submitted',
            'response' => $response,
            'submitted_at' => now(),
        ]);
    }

    public function markCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    public function markFailed($error)
    {
        $this->update([
            'status' => 'failed',
            'response' => $error,
        ]);
    }
}
