<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'reviewer_name',
        'rating',
        'review_text',
        'is_verified',
        'is_approved',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the service that owns the review
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    /**
     * Get the user that wrote the review
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Get average rating for a service
     */
    public static function getAverageRating($serviceId)
    {
        return self::where('service_id', $serviceId)
            ->approved()
            ->avg('rating') ?? 0;
    }

    /**
     * Get review count for a service
     */
    public static function getReviewCount($serviceId)
    {
        return self::where('service_id', $serviceId)
            ->approved()
            ->count();
    }

    /**
     * Get review statistics for a service (for rich snippets)
     */
    public static function getReviewStats($serviceId)
    {
        $reviews = self::where('service_id', $serviceId)->approved();

        return [
            'average_rating' => round($reviews->avg('rating') ?? 4.5, 1),
            'review_count' => $reviews->count() ?: 1,
            'best_rating' => 5,
            'worst_rating' => 1,
        ];
    }
}
