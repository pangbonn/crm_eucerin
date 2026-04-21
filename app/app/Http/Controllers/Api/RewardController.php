<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::where('is_active', true)
            ->orderBy('points_required')
            ->get()
            ->map(function ($r) {
                return [
                    'id'              => $r->id,
                    'name'            => $r->name,
                    'description'     => $r->description,
                    'image_url'       => $r->image ? Storage::url($r->image) : null,
                    'points_required' => $r->points_required,
                    'stock'           => $r->stock,
                ];
            });

        return response()->json($rewards);
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:rewards,id',
        ]);

        $user = $request->user();
        $reward = Reward::findOrFail($request->reward_id);

        if (!$reward->is_active || $reward->stock <= 0) {
            return response()->json(['message' => 'ของรางวัลหมดแล้วหรือปิดการแลก'], 422);
        }

        $totalPoints = $user->total_points;
        if ($totalPoints < $reward->points_required) {
            return response()->json(['message' => 'คะแนนไม่เพียงพอ (' . $totalPoints . '/' . $reward->points_required . ')'], 422);
        }

        // ตรวจ training points ถ้ากำหนดไว้
        if ($reward->training_points_required > 0) {
            $trainingPoints = Point::where('user_id', $user->id)
                ->whereIn('source', ['exam_pre', 'exam_post', 'vdo_stamp'])
                ->sum('points');
            if ($trainingPoints < $reward->training_points_required) {
                return response()->json(['message' => 'คะแนนจาก Exam ไม่เพียงพอ'], 422);
            }
        }

        // ดึงที่อยู่ user
        $address = $user->address;
        if (!$address) {
            return response()->json(['message' => 'ไม่พบข้อมูลที่อยู่ กรุณาติดต่อ Admin'], 422);
        }

        DB::transaction(function () use ($user, $reward, $address) {
            // ตัดคะแนน
            Point::create([
                'user_id'  => $user->id,
                'points'   => -$reward->points_required,
                'source'   => 'redemption',
                'note'     => 'แลก: ' . $reward->name,
            ]);

            // ลด stock
            $reward->decrement('stock');

            // สร้าง redemption
            RewardRedemption::create([
                'user_id'               => $user->id,
                'reward_id'             => $reward->id,
                'status'                => 'pending',
                'shipping_name'         => $user->name . ' ' . $user->lastname,
                'shipping_phone'        => $user->phone,
                'shipping_address'      => $address->address,
                'shipping_province'     => $address->province ? $address->province->name_th : '',
                'shipping_district'     => $address->district ? $address->district->name_th : '',
                'shipping_subdistrict'  => $address->subdistrict ? $address->subdistrict->name_th : '',
                'shipping_postal_code'  => $address->postal_code,
            ]);
        });

        return response()->json(['message' => 'แลกของรางวัลสำเร็จ รอการอนุมัติ']);
    }

    public function myRedemptions(Request $request)
    {
        $user = $request->user();
        $redemptions = RewardRedemption::where('user_id', $user->id)
            ->with('reward:id,name')
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id'          => $r->id,
                    'reward_name' => $r->reward ? $r->reward->name : '-',
                    'status'      => $r->status,
                    'points_used' => 0, // คำนวณจาก points table ถ้าต้องการ
                    'created_at'  => $r->created_at,
                ];
            });

        return response()->json($redemptions);
    }
}
