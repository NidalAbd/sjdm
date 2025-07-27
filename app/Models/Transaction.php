<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'type',
        'amount',
        'currency',
        'status',
        'api_cost',
        'profit',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function supportTicket()
    {
        return $this->hasOne(SupportTicket::class, 'ticketable_id')->where('ticketable_type', 'App\Models\Transaction');
    }
}
