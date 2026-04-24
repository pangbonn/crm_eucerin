<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QACategory extends Model
{
    protected $table = 'qa_categories';
    protected $fillable = ['name', 'order'];

    public function items() { return $this->hasMany(QAItem::class, 'category_id')->orderBy('order'); }
}
