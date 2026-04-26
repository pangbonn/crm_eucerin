<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            ['key' => 'site_name',      'label' => 'ชื่อระบบ',             'type' => 'text',    'value' => 'CRM Eucerin'],
            ['key' => 'site_logo',      'label' => 'โลโก้ระบบ',            'type' => 'image',   'value' => null],
            ['key' => 'sidebar_theme',  'label' => 'สี Sidebar',           'type' => 'select',  'value' => 'sidebar-dark-primary elevation-4'],
            ['key' => 'navbar_theme',   'label' => 'สี Navbar',            'type' => 'select',  'value' => 'navbar-white navbar-light'],
            ['key' => 'auth_btn_class', 'label' => 'สีปุ่ม Login',         'type' => 'select',  'value' => 'btn-flat btn-danger'],
            ['key' => 'stamp_max',      'label' => 'จำนวน Stamp สูงสุด',   'type' => 'number',  'value' => '8'],
            ['key' => 'stamp_points',   'label' => 'คะแนนต่อ 1 Stamp',     'type' => 'number',  'value' => '10'],
        ];

        foreach ($defaults as $item) {
            Setting::firstOrCreate(['key' => $item['key']], $item);
        }
    }
}
