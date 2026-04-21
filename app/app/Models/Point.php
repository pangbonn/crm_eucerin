<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'user_id', 'points', 'source',
        'reference_id', 'reference_type', 'note',
    ];

    protected $casts = ['points' => 'integer'];

    public function user() { return $this->belongsTo(User::class); }
}
