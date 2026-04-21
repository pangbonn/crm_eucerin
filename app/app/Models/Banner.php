<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'type', 'image_url', 'link_url',
        'condition_text', 'is_active', 'display_month', 'display_year',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function getActive(string $type): ?self
    {
        return self::where('type', $type)->where('is_active', true)->latest()->first();
    }
}
