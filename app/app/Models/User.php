<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'line_uid', 'name', 'lastname', 'phone', 'birthdate',
        'employee_code', 'start_year', 'consent_pdpa',
        'photo_url', 'level', 'is_active', 'resigned_at',
    ];

    protected $casts = [
        'birthdate'    => 'date',
        'consent_pdpa' => 'boolean',
        'is_active'    => 'boolean',
        'resigned_at'  => 'datetime',
    ];

    protected $hidden = [];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function address()
    {
        return $this->hasOne(UserAddress::class);
    }

    public function currentBranch()
    {
        return $this->hasOne(UserBranch::class)->latest();
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function examResults()
    {
        return $this->hasMany(UserExamResult::class);
    }

    public function redemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function getTotalPointsAttribute()
    {
        return $this->points()->sum('points');
    }

    // คะแนนทั้งหมด โดยไม่หักรายการแลกรางวัล (source = redemption)
    public function getReceiptPointsAttribute()
    {
        return (int) $this->points()
            ->where('source', '!=', 'redemption')
            ->where('points', '>', 0)
            ->sum('points');
    }

    public function getLevelMultiplierAttribute()
    {
        switch ($this->level) {
            case 'platinum': return 2.0;
            case 'silver':   return 1.5;
            default:         return 1.0;
        }
    }
}
