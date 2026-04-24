<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            ZoneSeeder::class,
            ShopTypeSeeder::class,
            ProvinceSeeder::class,
            BranchSeeder::class,
            SettingSeeder::class,
            ExamQuestionSeeder::class,
            QASeeder::class,
            ProductSeeder::class,
        ]);
    }
}
