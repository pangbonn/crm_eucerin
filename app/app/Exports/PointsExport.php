<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PointsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return User::withSum('points', 'points')->where('is_active', true)->orderByDesc('points_sum_points');
    }

    public function headings(): array
    {
        return ['ชื่อ', 'นามสกุล', 'รหัสพนักงาน', 'ระดับ', 'คะแนนรวม'];
    }

    public function map($user): array
    {
        return [
            $user->name, $user->lastname,
            $user->employee_code, ucfirst($user->level),
            $user->points_sum_points ?? 0,
        ];
    }
}
