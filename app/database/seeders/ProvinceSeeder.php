<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    public function run()
    {
        $dataPath = base_path('../liff/src/data');

        $provinces = json_decode(file_get_contents("{$dataPath}/province.json"), true);
        $amphures  = json_decode(file_get_contents("{$dataPath}/amphure.json"),  true);
        $tambons   = json_decode(file_get_contents("{$dataPath}/tambon.json"),   true);

        // Provinces
        $provinceRows = [];
        foreach ($provinces as $p) {
            if ($p['deleted_at']) continue;
            $provinceRows[] = [
                'id'      => $p['id'],
                'name_th' => $p['name_th'],
                'name_en' => $p['name_en'],
            ];
        }
        foreach (array_chunk($provinceRows, 100) as $chunk) {
            DB::table('provinces')->insertOrIgnore($chunk);
        }

        // Districts
        $districtRows = [];
        foreach ($amphures as $a) {
            if ($a['deleted_at']) continue;
            $districtRows[] = [
                'id'          => $a['id'],
                'province_id' => $a['province_id'],
                'name_th'     => $a['name_th'],
                'name_en'     => $a['name_en'],
            ];
        }
        foreach (array_chunk($districtRows, 200) as $chunk) {
            DB::table('districts')->insertOrIgnore($chunk);
        }

        // Subdistricts
        $subdistrictRows = [];
        foreach ($tambons as $t) {
            if ($t['deleted_at']) continue;
            $subdistrictRows[] = [
                'id'          => $t['id'],
                'district_id' => $t['district_id'],
                'name_th'     => $t['name_th'],
                'name_en'     => $t['name_en'],
                'postal_code' => $t['zip_code'] ? (string) $t['zip_code'] : null,
            ];
        }
        foreach (array_chunk($subdistrictRows, 500) as $chunk) {
            DB::table('subdistricts')->insertOrIgnore($chunk);
        }

        $this->command->info('Seeded: ' . count($provinceRows) . ' provinces, ' . count($districtRows) . ' districts, ' . count($subdistrictRows) . ' subdistricts');
    }
}
