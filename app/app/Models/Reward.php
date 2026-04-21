<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'name', 'description', 'image',
        'points_required', 'training_points_required',
        'stock', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function redemptions() { return $this->hasMany(RewardRedemption::class); }
}
