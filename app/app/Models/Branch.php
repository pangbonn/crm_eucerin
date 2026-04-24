<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = ['zone_id', 'shop_type_id', 'province_id', 'shop_name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function zone()     { return $this->belongsTo(Zone::class); }
    public function shopType() { return $this->belongsTo(ShopType::class); }
    public function province() { return $this->belongsTo(Province::class); }
}
