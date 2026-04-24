<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\RewardRedemption;
use App\Models\Point;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PointCalculationService
{
    public function approveReceipt(Receipt $receipt, float $salesAmount, array $skuData, $approver): int
    {
        $user = $receipt->user;

        // ถ้าอนุมัติซ้ำ (แก้ไขคะแนน) ให้ reverse คะแนนเดิมก่อน
        if ($receipt->status === 'approved' && $receipt->points_awarded > 0) {
            Point::create([
                'user_id'        => $user->id,
                'points'         => -$receipt->points_awarded,
                'source'         => 'receipt',
                'reference_id'   => $receipt->id,
                'reference_type' => Receipt::class,
                'note'           => "ยกเลิกคะแนนเดิม ใบเสร็จ #{$receipt->id} (แก้ไขโดย {$approver->name})",
            ]);
        }

        $basePoints  = $this->calculateBasePoints($salesAmount, $skuData);
        $multiplier  = $user->level_multiplier;
        $finalPoints = (int) round($basePoints * $multiplier);

        $receipt->update([
            'status'         => 'approved',
            'approved_by'    => $approver->id,
            'approved_at'    => now(),
            'sales_amount'   => $salesAmount,
            'sku_data'       => $skuData,
            'points_awarded' => $finalPoints,
        ]);

        $levelLabel = ucfirst($user->level);
        $note = $multiplier != 1.0
            ? "ใบเสร็จ #{$receipt->id} ยอด " . number_format($salesAmount, 2) . " บาท (base " . number_format($basePoints) . " × {$multiplier} {$levelLabel})"
            : "ใบเสร็จ #{$receipt->id} ยอด " . number_format($salesAmount, 2) . " บาท";

        Point::create([
            'user_id'        => $user->id,
            'points'         => $finalPoints,
            'source'         => 'receipt',
            'reference_id'   => $receipt->id,
            'reference_type' => Receipt::class,
            'note'           => $note,
        ]);

        return $finalPoints;
    }

    public function rejectReceipt(Receipt $receipt, string $note, $approver): void
    {
        $receipt->update([
            'status'      => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'note'        => $note,
        ]);
    }

    public function adjustPoints(User $user, int $points, string $note, $admin): void
    {
        Point::create([
            'user_id' => $user->id,
            'points'  => $points,
            'source'  => 'manual',
            'note'    => "[Admin: {$admin->name}] {$note}",
        ]);
    }

    public function cancelReceiptPoints(Receipt $receipt, $admin): void
    {
        DB::transaction(function () use ($receipt, $admin) {
            // Lock row เพื่อกัน double-submit
            $fresh = Receipt::lockForUpdate()->find($receipt->id);
            if (!$fresh || $fresh->status !== 'approved' || $fresh->points_awarded <= 0) {
                return;
            }

            Point::create([
                'user_id'        => $fresh->user_id,
                'points'         => -$fresh->points_awarded,
                'source'         => 'receipt',
                'reference_id'   => $fresh->id,
                'reference_type' => Receipt::class,
                'note'           => "ยกเลิกการอนุมัติใบเสร็จ #{$fresh->id} (โดย {$admin->name})",
            ]);

            $fresh->update([
                'status'         => 'cancelled',
                'approved_by'    => $admin->id,
                'approved_at'    => now(),
                'points_awarded' => 0,
            ]);
        });
    }

    public function deductRedemptionPoints(User $user, RewardRedemption $redemption): void
    {
        $points = $redemption->reward ? $redemption->reward->points_required : 0;
        if ($points <= 0) return;

        Point::create([
            'user_id'        => $user->id,
            'points'         => -$points,
            'source'         => 'redemption',
            'reference_id'   => $redemption->id,
            'reference_type' => RewardRedemption::class,
            'note'           => "แลกรางวัล: {$redemption->reward->name} #{$redemption->id}",
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
