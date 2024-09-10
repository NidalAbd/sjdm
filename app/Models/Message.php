<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['support_ticket_id', 'user_id', 'message', 'read_at', 'sender_role'];

    // Relationship to the User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to the Support Ticket
    public function supportTicket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    // Scope to get unread messages
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Mark the message as read
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }

}
