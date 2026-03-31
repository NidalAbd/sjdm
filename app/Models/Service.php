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
        'name_en',
        'name_ar',
        'category_en',
        'category_ar',
        'translations',
        'type',
        'rate',
        'cost',
        'min',
        'max',
        'refill',
        'cancel',
    ];

    protected $casts = [
        'translations' => 'array',
        'refill' => 'boolean',
        'cancel' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'service_id';
    }

    /**
     * Get the service name for a given language
     */
    public function getTranslatedName(string $lang = 'en'): string
    {
        if ($lang === 'en') return $this->name_en ?? '';
        if ($lang === 'ar') return $this->name_ar ?? '';

        $translations = $this->translations;
        return $translations['name'][$lang] ?? $this->name_en ?? '';
    }

    /**
     * Get the service category for a given language
     */
    public function getTranslatedCategory(string $lang = 'en'): string
    {
        if ($lang === 'en') return $this->category_en ?? '';
        if ($lang === 'ar') return $this->category_ar ?? '';

        $translations = $this->translations;
        return $translations['category'][$lang] ?? $this->category_en ?? '';
    }
}
