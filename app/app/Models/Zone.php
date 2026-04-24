<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['code', 'name'];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
