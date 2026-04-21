<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::firstOrCreate(
            ['email' => 'admin@eucerin.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('Admin@1234'),
            ]
        );
    }
}
