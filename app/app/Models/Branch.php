<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = ['zone', 'province_id', 'shop_type', 'shop_name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function province() { return $this->belongsTo(Province::class); }

    public static function zones(): array
    {
        return [
            'BKK1','BKK2','BKK3','BKK4','BKK6','BKK7','BKK8','BKK9',
            'NU','NL','NEU','NEM','NEL','MID','EAST','SOUTH','DS','OTHER',
        ];
    }

    public static function shopTypes(): array
    {
        return [
            'A&J','Beautrium','Boots','CDH','Chaingmai Dai rect','Counter Brand',
            'Eveandboy','Facebook','Fascino','Health Plus','ICARE Health','JE Muay',
            'Lab','Lazada','LineOA','Lotus\'s','Matsumoto','OTC BKK','OTC UPC',
            'Pure','Safe&Save','Sawatdee Direct','Shopee','Siam Drug',
            'U care','Watson','Win Cosmetics',
        ];
    }
}
