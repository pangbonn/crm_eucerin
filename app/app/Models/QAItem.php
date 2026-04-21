<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QAItem extends Model
{
    protected $fillable = ['category_id', 'question', 'answer', 'order'];

    public function category() { return $this->belongsTo(QACategory::class); }
}
