<?php

namespace Database\Seeders;

use App\Models\ShopType;
use Illuminate\Database\Seeder;

class ShopTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'A&J','Beautrium','Boots','CDH','Chaingmai Dai rect','Counter Brand',
            'Eveandboy','Facebook','Fascino','Health Plus','ICARE Health','JE Muay',
            'Lab','Lazada','LineOA','Lotus\'s','Matsumoto','OTC BKK','OTC UPC',
            'Pure','Safe&Save','Sawatdee Direct','Shopee','Siam Drug',
            'U care','Watson','Win Cosmetics',
        ];

        foreach ($types as $name) {
            ShopType::firstOrCreate(['name' => $name]);
        }
    }
}
