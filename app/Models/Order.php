<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id', 
        'link', 
        'quantity', 
        'runs', 
        'interval',
        'start_count',
        'remains',
        'charge',
        'status',
        'api_order_id',
        'can_refill',
        'can_cancel'
    ];

    protected $attributes = [
        'start_count' => null,
        'remains' => null,
        'can_refill' => false,
        'can_cancel' => false,
    ];

    protected $casts = [
        'charge' => 'decimal:7',
        'can_refill' => 'boolean',
        'can_cancel' => 'boolean',
        'start_count' => 'integer',
        'remains' => 'integer',
        'quantity' => 'integer',
        'runs' => 'integer',
        'interval' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function supportTicket()
    {
        return $this->hasOne(SupportTicket::class, 'ticketable_id')->where('ticketable_type', 'App\Models\Order');
    }

    /**
     * Calculate completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        if (!$this->start_count || $this->start_count <= 0) {
            return 0;
        }

        $completed = $this->start_count - ($this->remains ?? 0);
        return round(($completed / $this->start_count) * 100, 1);
    }

    /**
     * Calculate refund amount for partial orders
     */
    public function getRefundAmountAttribute()
    {
        if ($this->status !== 'partial' || !$this->charge || $this->charge <= 0) {
            return 0;
        }

        $completionPercentage = $this->completion_percentage;
        $refundAmount = $this->charge * (1 - ($completionPercentage / 100));
        
        return round($refundAmount, 2);
    }

    /**
     * Check if order is eligible for refund
     */
    public function isEligibleForRefund()
    {
        return $this->status === 'partial' && $this->charge > 0;
    }

    /**
     * Get order status with color class
     */
    public function getStatusColorAttribute()
    {
        $statusColors = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            'partial' => 'warning',
            'waiting' => 'primary'
        ];

        return $statusColors[$this->status] ?? 'secondary';
    }

    /**
     * Scope for partial orders
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope for orders with refunds
     */
    public function scopeWithRefunds($query)
    {
        return $query->where('status', 'partial')->where('charge', '>', 0);
    }
}
