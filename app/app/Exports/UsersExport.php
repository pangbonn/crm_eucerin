<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return User::with(['currentBranch.branch', 'address.province']);
    }

    public function headings(): array
    {
        return ['ชื่อ', 'นามสกุล', 'รหัสพนักงาน', 'เบอร์โทร', 'วันเกิด',
                'ปีที่เข้า', 'ระดับ', 'เขต', 'สาขา', 'สถานะ', 'วันสมัคร'];
    }

    public function map($user): array
    {
        $branch = $user->currentBranch->branch ?? null;
        return [
            $user->name,
            $user->lastname,
            $user->employee_code,
            $user->phone,
            $user->birthdate ? $user->birthdate->format('d/m/Y') : '-',
            $user->start_year,
            ucfirst($user->level),
            $branch ? $branch->zone : '-',
            $branch ? $branch->shop_name : '-',
            $user->is_active ? 'Active' : 'ลาออก',
            $user->created_at->format('d/m/Y'),
        ];
    }
}
