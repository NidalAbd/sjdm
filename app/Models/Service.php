<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Service extends Model
{

    use HasFactory;

    protected $primaryKey = 'service_id'; // Primary key is 'service_id' instead of 'id'

    protected $fillable = [
        'service_id', 'name', 'type', 'category', 'rate', 'min', 'max', 'refill', 'cancel',
    ];

    public function getRouteKeyName()
    {
        return 'service_id';
    }


}
