<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $branches = [
            ['zone' => 'BKK1', 'shop_type' => 'Boots',      'shop_name' => 'Boots Central World'],
            ['zone' => 'BKK1', 'shop_type' => 'Beautrium',  'shop_name' => 'Beautrium Siam'],
            ['zone' => 'BKK2', 'shop_type' => 'Watson',     'shop_name' => 'Watson Emporium'],
            ['zone' => 'BKK3', 'shop_type' => 'Lotus\'s',   'shop_name' => 'Lotus\'s Lat Phrao'],
            ['zone' => 'DS',   'shop_type' => 'Shopee',     'shop_name' => 'Shopee Official'],
            ['zone' => 'DS',   'shop_type' => 'Lazada',     'shop_name' => 'Lazada Official'],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['zone' => $branch['zone'], 'shop_type' => $branch['shop_type'], 'shop_name' => $branch['shop_name']],
                $branch
            );
        }
    }
}
