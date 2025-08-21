<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticketable_id',
        'ticketable_type',
        'subject',
        'message',
        'status_id',
        'type',
        'subtype',
    ];

    public function ticketable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
