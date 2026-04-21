<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
    protected $fillable = ['district_id', 'name_th', 'name_en', 'postal_code'];

    public function district() { return $this->belongsTo(District::class); }
}
