<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'link', 'quantity', 'runs', 'interval', 'status', 'system_status', 'refunded_percent', 'charge', 'api_order_id', 'start_count', 'remains'
    ];
    protected $attributes = [
        'start_count' => null,
        'remains' => null,
    ];
    public function user()
    {
        // Include soft-deleted users so refunds/transactions still link correctly
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    // In your Order model
    public function supportTicket()
    {
        return $this->hasOne(SupportTicket::class, 'ticketable_id')->where('ticketable_type', 'App\Models\Order');
    }


}
