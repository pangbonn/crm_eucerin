<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'user_id', 'images', 'sales_amount', 'sku_data',
        'status', 'approved_by', 'approved_at', 'points_awarded', 'note',
    ];

    protected $casts = [
        'images'      => 'array',
        'sku_data'    => 'array',
        'approved_at' => 'datetime',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function approver() { return $this->belongsTo(Admin::class, 'approved_by'); }
}
