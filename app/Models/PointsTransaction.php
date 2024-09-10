<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class PointsTransaction extends Model
{
    use HasApiTokens,  Notifiable, HasRoles;

    protected $fillable = [
        'user_id',
        'points',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
