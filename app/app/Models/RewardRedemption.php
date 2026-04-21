<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRedemption extends Model
{
    protected $fillable = [
        'user_id', 'reward_id', 'status',
        'shipping_name', 'shipping_phone', 'shipping_address',
        'shipping_province', 'shipping_district', 'shipping_subdistrict',
        'shipping_postal_code', 'approved_by', 'approved_at', 'note',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    public function user()     { return $this->belongsTo(User::class); }
    public function reward()   { return $this->belongsTo(Reward::class); }
    public function approver() { return $this->belongsTo(Admin::class, 'approved_by'); }
}
