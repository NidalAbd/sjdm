<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['locale', 'group', 'key', 'value'];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'locale', 'code');
    }

    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    public function scopeForGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function getFullKeyAttribute(): string
    {
        return "{$this->group}.{$this->key}";
    }

    public static function getTranslationsForLocale(string $locale): array
    {
        return Cache::remember("translations_{$locale}", 3600, function () use ($locale) {
            $translations = self::forLocale($locale)->get();
            $result = [];
            foreach ($translations as $translation) {
                $result[$translation->full_key] = $translation->value;
            }
            return $result;
        });
    }

    public static function setTranslation(string $locale, string $group, string $key, string $value): self
    {
        $translation = self::updateOrCreate(
            ['locale' => $locale, 'group' => $group, 'key' => $key],
            ['value' => $value]
        );
        self::clearCache($locale, $group);
        return $translation;
    }

    public static function setTranslations(string $locale, array $translations): int
    {
        $count = 0;
        foreach ($translations as $fullKey => $value) {
            $parts = explode('.', $fullKey, 2);
            $group = count($parts) > 1 ? $parts[0] : 'app';
            $key = count($parts) > 1 ? $parts[1] : $parts[0];
            self::updateOrCreate(
                ['locale' => $locale, 'group' => $group, 'key' => $key],
                ['value' => $value]
            );
            $count++;
        }
        self::clearCache($locale);
        return $count;
    }

    public static function clearCache(?string $locale = null, ?string $group = null): void
    {
        if ($locale && $group) Cache::forget("translations_{$locale}_{$group}");
        if ($locale) Cache::forget("translations_{$locale}");
        Cache::forget('translation_groups');
        Cache::forget('translations_version');
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(fn($t) => self::clearCache($t->locale, $t->group));
        static::deleted(fn($t) => self::clearCache($t->locale, $t->group));
    }
}
