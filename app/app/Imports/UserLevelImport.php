<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class UserLevelImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['employee_code']) || empty($row['level'])) continue;

            $level = strtolower(trim($row['level']));
            if (!in_array($level, ['gold', 'silver', 'platinum'])) continue;

            User::where('employee_code', trim($row['employee_code']))
                ->update(['level' => $level]);
        }
    }
}
