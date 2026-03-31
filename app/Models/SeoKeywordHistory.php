<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoKeywordHistory extends Model
{
    use HasFactory;

    protected $table = 'seo_keyword_history';

    protected $fillable = [
        'keyword_id',
        'impressions',
        'clicks',
        'ctr',
        'position',
        'date',
    ];

    protected $casts = [
        'ctr' => 'decimal:4',
        'position' => 'decimal:2',
        'date' => 'date',
    ];

    public function keyword()
    {
        return $this->belongsTo(SeoKeyword::class, 'keyword_id');
    }
}
