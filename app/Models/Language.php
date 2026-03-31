<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'native_name', 'direction',
        'is_active', 'is_default', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'locale', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    public static function getDefault(): ?self
    {
        return Cache::remember('language_default', 3600, function () {
            return self::where('is_default', true)->first();
        });
    }

    public static function getActiveOrdered()
    {
        return Cache::remember('languages_active', 3600, function () {
            return self::active()->ordered()->get();
        });
    }

    public static function getByCode(string $code): ?self
    {
        return Cache::remember("language_{$code}", 3600, function () use ($code) {
            return self::where('code', $code)->first();
        });
    }

    public function getTranslationsVersionHash(): string
    {
        $latestUpdate = $this->translations()->max('updated_at');
        $count = $this->translations()->count();
        return md5($this->code . $latestUpdate . $count);
    }

    public static function clearCache(): void
    {
        Cache::forget('language_default');
        Cache::forget('languages_active');
        Cache::forget('translations_version');
        self::all()->each(function ($language) {
            Cache::forget("language_{$language->code}");
            Cache::forget("translations_{$language->code}");
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(fn() => self::clearCache());
        static::deleted(fn() => self::clearCache());
    }
}
