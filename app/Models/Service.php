<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Service extends Model
{

    use HasFactory;

    protected $primaryKey = 'service_id'; // Primary key is 'service_id' instead of 'id'
    public $incrementing = false; // Assuming the ID is not auto-incrementing
    protected $keyType = 'unsignedBigInteger'; // Adjust based on your needs


    protected $fillable = [
        'service_id',
        'name_en',   // Added for English name
        'name_ar',   // Added for Arabic name
        'category_en', // Added for English category
        'category_ar', // Added for Arabic category
        'type',
        'rate',
        'min',
        'max',
        'refill',
        'cancel',
    ];

    public function getRouteKeyName()
    {
        return 'service_id';
    }


}
