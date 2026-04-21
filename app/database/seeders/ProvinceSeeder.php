<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\District;
use App\Models\Subdistrict;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        // ข้อมูลตัวอย่าง — production ควร import จาก dataset จังหวัด/อำเภอ/ตำบลเต็ม
        $data = [
            [
                'name_th' => 'กรุงเทพมหานคร',
                'name_en' => 'Bangkok',
                'districts' => [
                    [
                        'name_th' => 'พระนคร',
                        'name_en' => 'Phra Nakhon',
                        'subdistricts' => [
                            ['name_th' => 'พระบรมมหาราชวัง', 'postal_code' => '10200'],
                            ['name_th' => 'วังบูรพาภิรมย์',   'postal_code' => '10200'],
                        ],
                    ],
                    [
                        'name_th' => 'ดุสิต',
                        'name_en' => 'Dusit',
                        'subdistricts' => [
                            ['name_th' => 'ดุสิต',     'postal_code' => '10300'],
                            ['name_th' => 'วชิรพยาบาล', 'postal_code' => '10300'],
                        ],
                    ],
                ],
            ],
            [
                'name_th' => 'เชียงใหม่',
                'name_en' => 'Chiang Mai',
                'districts' => [
                    [
                        'name_th' => 'เมืองเชียงใหม่',
                        'name_en' => 'Mueang Chiang Mai',
                        'subdistricts' => [
                            ['name_th' => 'ศรีภูมิ',  'postal_code' => '50200'],
                            ['name_th' => 'พระสิงห์', 'postal_code' => '50200'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($data as $pData) {
            $province = Province::firstOrCreate(
                ['name_th' => $pData['name_th']],
                ['name_en' => $pData['name_en']]
            );

            foreach ($pData['districts'] as $dData) {
                $district = District::firstOrCreate(
                    ['province_id' => $province->id, 'name_th' => $dData['name_th']],
                    ['name_en' => $dData['name_en'] ?? null]
                );

                foreach ($dData['subdistricts'] as $sData) {
                    Subdistrict::firstOrCreate(
                        ['district_id' => $district->id, 'name_th' => $sData['name_th']],
                        ['postal_code' => $sData['postal_code']]
                    );
                }
            }
        }
    }
}
