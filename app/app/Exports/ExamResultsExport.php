<?php

namespace App\Exports;

use App\Models\UserExamResult;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamResultsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return UserExamResult::with(['user', 'examPart'])->orderByDesc('completed_at');
    }

    public function headings(): array
    {
        return ['วันที่', 'ชื่อ', 'นามสกุล', 'รหัสพนักงาน', 'Part', 'Section', 'คะแนน', 'เต็ม', '%', 'Stamp'];
    }

    public function map($r): array
    {
        return [
            $r->completed_at ? $r->completed_at->format('d/m/Y H:i') : '-',
            $r->user ? $r->user->name : '-',
            $r->user ? $r->user->lastname : '-',
            $r->user ? $r->user->employee_code : '-',
            $r->examPart ? $r->examPart->title : '-',
            strtoupper($r->section),
            $r->score,
            $r->max_score,
            number_format($r->percentage, 1) . '%',
            $r->stamp_earned ? 'ได้' : '-',
        ];
    }
}
