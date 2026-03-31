<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoContentIdea extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_keyword',
        'related_keywords',
        'content_type',
        'priority',
        'status',
        'estimated_traffic',
        'source',
    ];

    protected $casts = [
        'related_keywords' => 'array',
    ];

    const CONTENT_TYPES = [
        'blog' => 'Blog Post',
        'service_page' => 'Service Page',
        'faq' => 'FAQ Section',
        'landing_page' => 'Landing Page',
        'guide' => 'Guide/Tutorial',
    ];

    const PRIORITIES = [
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Low',
    ];

    const STATUSES = [
        'pending' => 'Pending Review',
        'approved' => 'Approved',
        'in_progress' => 'In Progress',
        'published' => 'Published',
        'rejected' => 'Rejected',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }
}
