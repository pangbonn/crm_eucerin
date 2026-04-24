<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run()
    {
        $zones = [
            ['code' => 'BKK1',  'name' => 'BKK 1'],
            ['code' => 'BKK2',  'name' => 'BKK 2'],
            ['code' => 'BKK3',  'name' => 'BKK 3'],
            ['code' => 'BKK4',  'name' => 'BKK 4'],
            ['code' => 'BKK6',  'name' => 'BKK 6'],
            ['code' => 'BKK7',  'name' => 'BKK 7'],
            ['code' => 'BKK8',  'name' => 'BKK 8'],
            ['code' => 'BKK9',  'name' => 'BKK 9'],
            ['code' => 'NU',    'name' => 'NU'],
            ['code' => 'NL',    'name' => 'NL'],
            ['code' => 'NEU',   'name' => 'NEU'],
            ['code' => 'NEM',   'name' => 'NEM'],
            ['code' => 'NEL',   'name' => 'NEL'],
            ['code' => 'MID',   'name' => 'MID'],
            ['code' => 'EAST',  'name' => 'EAST'],
            ['code' => 'SOUTH', 'name' => 'SOUTH'],
            ['code' => 'DS',    'name' => 'DS'],
            ['code' => 'OTHER', 'name' => 'อื่นๆ'],
        ];

        foreach ($zones as $zone) {
            Zone::firstOrCreate(['code' => $zone['code']], $zone);
        }
    }
}
