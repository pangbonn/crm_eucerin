<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBranch extends Model
{
    protected $fillable = ['user_id', 'branch_id', 'channel', 'assigned_at'];

    protected $casts = ['assigned_at' => 'datetime'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function user()   { return $this->belongsTo(User::class); }
}
