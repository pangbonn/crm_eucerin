<?php

namespace App\Exports;

use App\Models\Receipt;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReceiptProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $rows = new Collection();

        $receipts = Receipt::with('user')
            ->whereIn('status', ['approved', 'cancelled'])
            ->orderByDesc('created_at')
            ->get();

        foreach ($receipts as $receipt) {
            $userName = $receipt->user ? ($receipt->user->name . ' ' . $receipt->user->lastname) : '-';
            $employeeCode = $receipt->user ? $receipt->user->employee_code : '-';
            $skuData = is_array($receipt->sku_data) ? $receipt->sku_data : [];

            if (empty($skuData)) {
                $rows->push([
                    'receipt_id' => $receipt->id,
                    'status' => $receipt->status,
                    'submitted_at' => $receipt->created_at ? $receipt->created_at->format('d/m/Y H:i') : '-',
                    'employee_code' => $employeeCode,
                    'employee_name' => $userName,
                    'sales_amount' => $receipt->sales_amount,
                    'points_awarded' => $receipt->points_awarded,
                    'sku' => '-',
                    'qty' => 0,
                    'point_per_unit' => 0,
                    'line_points' => 0,
                ]);
                continue;
            }

            foreach ($skuData as $item) {
                $qty = (int) ($item['qty'] ?? 0);
                $pointPerUnit = (float) ($item['points'] ?? 0);

                $rows->push([
                    'receipt_id' => $receipt->id,
                    'status' => $receipt->status,
                    'submitted_at' => $receipt->created_at ? $receipt->created_at->format('d/m/Y H:i') : '-',
                    'employee_code' => $employeeCode,
                    'employee_name' => $userName,
                    'sales_amount' => $receipt->sales_amount,
                    'points_awarded' => $receipt->points_awarded,
                    'sku' => $item['sku'] ?? '-',
                    'qty' => $qty,
                    'point_per_unit' => $pointPerUnit,
                    'line_points' => $qty * $pointPerUnit,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Receipt ID',
            'Status',
            'Submitted At',
            'Employee Code',
            'Employee Name',
            'Sales Amount',
            'Points Awarded',
            'SKU',
            'Qty',
            'Point/Unit',
            'Line Points',
        ];
    }
}
