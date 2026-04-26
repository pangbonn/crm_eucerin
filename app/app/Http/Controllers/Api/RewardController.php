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
                    'image_url'       => $r->image ? url(Storage::url($r->image)) : null,
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
            'use_registered_address' => 'required|boolean',
            'shipping_name' => 'required|string|max:150',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_province' => 'required|string|max:100',
            'shipping_district' => 'required|string|max:100',
            'shipping_subdistrict' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_carrier' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
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

        // ดึงที่อยู่ user สำหรับกรณีใช้ที่อยู่ลงทะเบียน
        $address = $user->address;
        $useRegisteredAddress = (bool) $request->boolean('use_registered_address');
        if ($useRegisteredAddress && !$address) {
            return response()->json(['message' => 'ไม่พบข้อมูลที่อยู่ กรุณาติดต่อ Admin'], 422);
        }

        $shippingData = $useRegisteredAddress
            ? [
                'shipping_name' => trim(($user->name ?: '') . ' ' . ($user->lastname ?: '')),
                'shipping_phone' => $user->phone ?: '',
                'shipping_address' => $address ? $address->address : '',
                'shipping_province' => $address ? $address->province_name : '',
                'shipping_district' => $address ? $address->district_name : '',
                'shipping_subdistrict' => $address ? $address->subdistrict_name : '',
                'shipping_postal_code' => $address ? $address->postal_code : '',
            ]
            : [
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_province' => $request->shipping_province,
                'shipping_district' => $request->shipping_district,
                'shipping_subdistrict' => $request->shipping_subdistrict,
                'shipping_postal_code' => $request->shipping_postal_code,
            ];

        DB::transaction(function () use ($user, $reward, $request, $useRegisteredAddress, $shippingData) {
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
                'use_registered_address'=> $useRegisteredAddress,
                'shipping_name'         => $shippingData['shipping_name'],
                'shipping_phone'        => $shippingData['shipping_phone'],
                'shipping_address'      => $shippingData['shipping_address'],
                'shipping_province'     => $shippingData['shipping_province'],
                'shipping_district'     => $shippingData['shipping_district'],
                'shipping_subdistrict'  => $shippingData['shipping_subdistrict'],
                'shipping_postal_code'  => $shippingData['shipping_postal_code'],
                'shipping_carrier'      => $request->shipping_carrier,
                'tracking_number'       => $request->tracking_number,
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
                    'id' => $r->id,
                    'reward_name' => $r->reward ? $r->reward->name : '-',
                    'quantity' => 1,
                    'status' => $r->status,
                    'created_at' => $r->created_at,
                    'shipping_name' => $r->shipping_name,
                    'shipping_phone' => $r->shipping_phone,
                    'shipping_address' => $r->shipping_address,
                    'shipping_subdistrict' => $r->shipping_subdistrict,
                    'shipping_district' => $r->shipping_district,
                    'shipping_province' => $r->shipping_province,
                    'shipping_postal_code' => $r->shipping_postal_code,
                    'shipping_carrier' => $r->shipping_carrier,
                    'tracking_number' => $r->tracking_number,
                ];
            });

        return response()->json($redemptions);
    }
}
