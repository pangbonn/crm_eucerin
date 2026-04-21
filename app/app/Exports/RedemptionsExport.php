<?php

namespace App\Exports;

use App\Models\RewardRedemption;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RedemptionsExport implements FromQuery, WithHeadings, WithMapping
{
    private $month;
    private $year;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function query()
    {
        $query = RewardRedemption::with(['user', 'reward'])->where('status', 'approved');

        if ($this->month && $this->year) {
            $query->whereMonth('approved_at', $this->month)->whereYear('approved_at', $this->year);
        }

        return $query->orderByDesc('approved_at');
    }

    public function headings(): array
    {
        return ['วันที่อนุมัติ', 'ชื่อ', 'นามสกุล', 'รหัสพนักงาน', 'เบอร์โทร',
                'ของรางวัล', 'ที่อยู่จัดส่ง', 'จังหวัด', 'รหัสไปรษณีย์'];
    }

    public function map($r): array
    {
        return [
            $r->approved_at ? $r->approved_at->format('d/m/Y') : '-',
            $r->user ? $r->user->name : '-',
            $r->user ? $r->user->lastname : '-',
            $r->user ? $r->user->employee_code : '-',
            $r->shipping_phone,
            $r->reward ? $r->reward->name : '-',
            $r->shipping_address,
            $r->shipping_province,
            $r->shipping_postal_code,
        ];
    }
}
