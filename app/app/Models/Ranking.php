<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'total_points', 'rank', 'period_month', 'period_year',
    ];

    protected $casts = ['updated_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}
