<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\Point;
use App\Models\User;

class PointCalculationService
{
    public function approveReceipt(Receipt $receipt, float $salesAmount, array $skuData, User $approver): int
    {
        $user = $receipt->user;

        // base points จาก sales amount (ทุก 1 บาท = 1 point หรือปรับได้ตาม SKU)
        $basePoints = $this->calculateBasePoints($salesAmount, $skuData);

        // คูณ multiplier ตามระดับพนักงาน
        $finalPoints = (int) round($basePoints * $user->level_multiplier);

        $receipt->update([
            'status'         => 'approved',
            'approved_by'    => $approver->id,
            'approved_at'    => now(),
            'sales_amount'   => $salesAmount,
            'sku_data'       => $skuData,
            'points_awarded' => $finalPoints,
        ]);

        Point::create([
            'user_id'        => $user->id,
            'points'         => $finalPoints,
            'source'         => 'receipt',
            'reference_id'   => $receipt->id,
            'reference_type' => Receipt::class,
            'note'           => "ใบเสร็จ #{$receipt->id} ยอด " . number_format($salesAmount, 2) . " บาท",
        ]);

        return $finalPoints;
    }

    public function rejectReceipt(Receipt $receipt, string $note, User $approver): void
    {
        $receipt->update([
            'status'      => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'note'        => $note,
        ]);
    }

    public function adjustPoints(User $user, int $points, string $note, User $admin): void
    {
        Point::create([
            'user_id' => $user->id,
            'points'  => $points,
            'source'  => 'manual',
            'note'    => "[Admin: {$admin->name}] {$note}",
        ]);
    }

    public function refundRedemptionPoints(User $user, int $points, int $redemptionId): void
    {
        Point::create([
            'user_id'        => $user->id,
            'points'         => $points,
            'source'         => 'manual',
            'reference_id'   => $redemptionId,
            'note'           => "คืนคะแนนจากการปฏิเสธการแลกรางวัล #{$redemptionId}",
        ]);
    }

    private function calculateBasePoints(float $salesAmount, array $skuData): float
    {
        // ถ้ามี SKU data ให้คำนวณจาก SKU แต่ละตัว
        if (!empty($skuData)) {
            $total = 0;
            foreach ($skuData as $sku) {
                $total += (float)($sku['points'] ?? 0) * (int)($sku['qty'] ?? 1);
            }
            return $total > 0 ? $total : $salesAmount;
        }

        // fallback: 1 บาท = 1 คะแนน
        return $salesAmount;
    }
}
