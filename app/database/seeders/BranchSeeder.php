<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ShopType;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        // province_id: 1=กทม, 2=สมุทรปราการ, 3=นนทบุรี, 4=ปทุมธานี,
        //              11=ชลบุรี, 19=นครราชสีมา, 28=ขอนแก่น, 29=อุดรธานี,
        //              38=เชียงใหม่, 66=ภูเก็ต, 67=สุราษฎร์ธานี, 70=สงขลา
        $branches = [
            // BKK1 — กรุงเทพ (ใจกลาง/สยาม/สีลม)
            ['zone' => 'BKK1', 'shop_type' => 'Boots',         'shop_name' => 'Boots Central World',         'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'Boots',         'shop_name' => 'Boots Siam Paragon',          'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'Boots',         'shop_name' => 'Boots EmQuartier',            'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'Watson',        'shop_name' => 'Watson CentralWorld',         'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'Watson',        'shop_name' => 'Watson Siam Paragon',         'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'Beautrium',     'shop_name' => 'Beautrium Siam Square One',   'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'Eveandboy',     'shop_name' => 'Eveandboy CentralWorld',      'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'Matsumoto',     'shop_name' => 'Matsumoto Siam Square',       'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'Pure',          'shop_name' => 'Pure Phrom Phong',            'province_id' => 1],
            ['zone' => 'BKK1', 'shop_type' => 'OTC BKK',       'shop_name' => 'OTC BKK Silom',               'province_id' => 1],

            // BKK2 — กรุงเทพ (อโศก/ทองหล่อ/เอกมัย)
            ['zone' => 'BKK2', 'shop_type' => 'Boots',         'shop_name' => 'Boots Terminal 21',           'province_id' => 1],
            ['zone' => 'BKK2', 'shop_type' => 'Beautrium',     'shop_name' => 'Beautrium Terminal 21',       'province_id' => 1],
            ['zone' => 'BKK2', 'shop_type' => 'Eveandboy',     'shop_name' => 'Eveandboy Platinum',          'province_id' => 1],
            ['zone' => 'BKK2', 'shop_type' => 'Watson',        'shop_name' => 'Watson Big C Ratchadamri',    'province_id' => 1],
            ['zone' => 'BKK2', 'shop_type' => 'U care',        'shop_name' => 'U care Asok',                 'province_id' => 1],

            // BKK3 — กรุงเทพ (ลาดพร้าว/รามคำแหง)
            ['zone' => 'BKK3', 'shop_type' => 'Boots',         'shop_name' => 'Boots The Mall Bangkapi',     'province_id' => 1],
            ['zone' => 'BKK3', 'shop_type' => 'Watson',        'shop_name' => 'Watson The Mall Bangkapi',    'province_id' => 1],
            ['zone' => 'BKK3', 'shop_type' => 'Lotus\'s',      'shop_name' => 'Lotus\'s Lat Phrao',          'province_id' => 1],
            ['zone' => 'BKK3', 'shop_type' => 'Counter Brand', 'shop_name' => 'Counter Brand Central Ladprao','province_id' => 1],

            // BKK4 — กรุงเทพ (พระราม 9/เพชรบุรีตัดใหม่)
            ['zone' => 'BKK4', 'shop_type' => 'Boots',         'shop_name' => 'Boots Central Rama 9',        'province_id' => 1],
            ['zone' => 'BKK4', 'shop_type' => 'Watson',        'shop_name' => 'Watson Central Rama 9',       'province_id' => 1],
            ['zone' => 'BKK4', 'shop_type' => 'Health Plus',   'shop_name' => 'Health Plus Rama 9',          'province_id' => 1],

            // BKK6 — สมุทรปราการ / บางนา
            ['zone' => 'BKK6', 'shop_type' => 'Boots',         'shop_name' => 'Boots Central Bangna',        'province_id' => 2],
            ['zone' => 'BKK6', 'shop_type' => 'Watson',        'shop_name' => 'Watson Mega Bangna',          'province_id' => 2],
            ['zone' => 'BKK6', 'shop_type' => 'Eveandboy',     'shop_name' => 'Eveandboy Mega Bangna',       'province_id' => 2],
            ['zone' => 'BKK6', 'shop_type' => 'Lotus\'s',      'shop_name' => 'Lotus\'s Bangna',             'province_id' => 2],

            // BKK7 — นนทบุรี (เซ็นทรัลเวสต์เกต/แจ้งวัฒนะ)
            ['zone' => 'BKK7', 'shop_type' => 'Boots',         'shop_name' => 'Boots Central Westgate',      'province_id' => 3],
            ['zone' => 'BKK7', 'shop_type' => 'Watson',        'shop_name' => 'Watson Central Westgate',     'province_id' => 3],
            ['zone' => 'BKK7', 'shop_type' => 'Counter Brand', 'shop_name' => 'Counter Brand The Mall Ngamwongwan', 'province_id' => 3],

            // BKK8 — ปิ่นเกล้า / ตลิ่งชัน
            ['zone' => 'BKK8', 'shop_type' => 'Boots',         'shop_name' => 'Boots Central Pinklao',       'province_id' => 1],
            ['zone' => 'BKK8', 'shop_type' => 'U care',        'shop_name' => 'U care Central Pinklao',      'province_id' => 1],

            // BKK9 — ปทุมธานี / รังสิต
            ['zone' => 'BKK9', 'shop_type' => 'Watson',        'shop_name' => 'Watson Future Park Rangsit',  'province_id' => 4],
            ['zone' => 'BKK9', 'shop_type' => 'Boots',         'shop_name' => 'Boots Future Park Rangsit',   'province_id' => 4],
            ['zone' => 'BKK9', 'shop_type' => 'Lotus\'s',      'shop_name' => 'Lotus\'s Rangsit',            'province_id' => 4],

            // EAST — ชลบุรี / พัทยา
            ['zone' => 'EAST', 'shop_type' => 'Boots',         'shop_name' => 'Boots Central Pattaya',       'province_id' => 11],
            ['zone' => 'EAST', 'shop_type' => 'Watson',        'shop_name' => 'Watson Central Pattaya',      'province_id' => 11],
            ['zone' => 'EAST', 'shop_type' => 'Counter Brand', 'shop_name' => 'Counter Brand Central Chonburi','province_id' => 11],
            ['zone' => 'EAST', 'shop_type' => 'Fascino',       'shop_name' => 'Fascino Chonburi',            'province_id' => 11],

            // NEL — นครราชสีมา
            ['zone' => 'NEL',  'shop_type' => 'Boots',         'shop_name' => 'Boots Central Korat',         'province_id' => 19],
            ['zone' => 'NEL',  'shop_type' => 'Watson',        'shop_name' => 'Watson Central Korat',        'province_id' => 19],
            ['zone' => 'NEL',  'shop_type' => 'Lotus\'s',      'shop_name' => 'Lotus\'s Korat',              'province_id' => 19],

            // NEU — ขอนแก่น / อุดรธานี
            ['zone' => 'NEU',  'shop_type' => 'Watson',        'shop_name' => 'Watson Khon Kaen',            'province_id' => 28],
            ['zone' => 'NEU',  'shop_type' => 'Boots',         'shop_name' => 'Boots Central Khon Kaen',     'province_id' => 28],
            ['zone' => 'NEU',  'shop_type' => 'A&J',           'shop_name' => 'A&J Khon Kaen',               'province_id' => 28],
            ['zone' => 'NEU',  'shop_type' => 'Counter Brand', 'shop_name' => 'Counter Brand Udon Thani',    'province_id' => 29],
            ['zone' => 'NEU',  'shop_type' => 'Watson',        'shop_name' => 'Watson Udon Thani',           'province_id' => 29],

            // NU — เชียงใหม่
            ['zone' => 'NU',   'shop_type' => 'Boots',         'shop_name' => 'Boots Central Festival Chiangmai','province_id' => 38],
            ['zone' => 'NU',   'shop_type' => 'Watson',        'shop_name' => 'Watson Maya Chiangmai',       'province_id' => 38],
            ['zone' => 'NU',   'shop_type' => 'Beautrium',     'shop_name' => 'Beautrium Central Chiangmai', 'province_id' => 38],
            ['zone' => 'NU',   'shop_type' => 'Chaingmai Dai rect', 'shop_name' => 'Chiangmai Direct เชียงใหม่', 'province_id' => 38],
            ['zone' => 'NU',   'shop_type' => 'Matsumoto',     'shop_name' => 'Matsumoto Promenada Chiangmai','province_id' => 38],

            // SOUTH — ภูเก็ต / สุราษฎร์ธานี / สงขลา
            ['zone' => 'SOUTH','shop_type' => 'Boots',         'shop_name' => 'Boots Central Phuket',        'province_id' => 66],
            ['zone' => 'SOUTH','shop_type' => 'Watson',        'shop_name' => 'Watson Central Phuket',       'province_id' => 66],
            ['zone' => 'SOUTH','shop_type' => 'Boots',         'shop_name' => 'Boots Central Suratthani',    'province_id' => 67],
            ['zone' => 'SOUTH','shop_type' => 'Watson',        'shop_name' => 'Watson Suratthani',           'province_id' => 67],
            ['zone' => 'SOUTH','shop_type' => 'Watson',        'shop_name' => 'Watson Lee Garden Hatyai',    'province_id' => 70],
            ['zone' => 'SOUTH','shop_type' => 'Boots',         'shop_name' => 'Boots Central Hatyai',        'province_id' => 70],

            // DS — Digital Sales (ไม่มีจังหวัด)
            ['zone' => 'DS',   'shop_type' => 'Shopee',        'shop_name' => 'Shopee Official',             'province_id' => null],
            ['zone' => 'DS',   'shop_type' => 'Lazada',        'shop_name' => 'Lazada Official',             'province_id' => null],
            ['zone' => 'DS',   'shop_type' => 'LineOA',        'shop_name' => 'LINE OA Official',            'province_id' => null],
            ['zone' => 'DS',   'shop_type' => 'Facebook',      'shop_name' => 'Facebook Official',           'province_id' => null],
        ];

        $zoneMap     = Zone::pluck('id', 'code');
        $shopTypeMap = ShopType::pluck('id', 'name');

        foreach ($branches as $b) {
            Branch::firstOrCreate(
                ['shop_name' => $b['shop_name']],
                [
                    'zone_id'      => $zoneMap[$b['zone']],
                    'shop_type_id' => $shopTypeMap[$b['shop_type']],
                    'province_id'  => $b['province_id'],
                    'shop_name'    => $b['shop_name'],
                ]
            );
        }

        $this->command->info('Seeded: ' . count($branches) . ' branches');
    }
}
