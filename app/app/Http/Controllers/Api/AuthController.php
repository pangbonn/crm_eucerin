<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * LINE Login → ถ้ามี user คืน JWT, ถ้าไม่มีบัญชีคืน needs_register
     */
    public function login(Request $request)
    {
        $request->validate([
            'line_uid'          => 'required|string',
            'line_access_token' => 'required|string',
            'display_name'      => 'nullable|string',
            'picture_url'       => 'nullable|string',
        ]);

        // ตรวจสอบ access token กับ LINE — ใช้ /v2/profile เพื่อได้ userId
        $lineProfile = $this->verifyLineToken($request->line_access_token);
        if (!$lineProfile || ($lineProfile['userId'] ?? null) !== $request->line_uid) {
            return response()->json(['message' => 'LINE token ไม่ถูกต้อง'], 401);
        }

        $user = User::where('line_uid', $request->line_uid)->first();

        if (!$user) {
            return response()->json(['needs_register' => true, 'line_uid' => $request->line_uid]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'บัญชีถูกระงับการใช้งาน'], 403);
        }

        // อัพเดทรูปโปรไฟล์ LINE ล่าสุด
        if ($request->picture_url) {
            $user->update(['photo_url' => $request->picture_url]);
        }

        $token = JWTAuth::fromUser($user);
        return response()->json([
            'token' => $token,
            'user'  => $this->userPayload($user),
        ]);
    }

    /**
     * ลงทะเบียนพนักงานใหม่
     */
    public function register(Request $request)
    {
        $request->validate([
            'line_uid'          => 'required|string|unique:users,line_uid',
            'line_display_name' => 'nullable|string',
            'line_picture_url'  => 'nullable|string',
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'phone'             => 'required|digits:10',
            'employee_code'     => 'nullable|string|max:50',
            'birthday'          => 'nullable|date|before:today',
            'address_line'      => 'required|string|max:500',
            'province_name'     => 'required|string|max:100',
            'district_name'     => 'required|string|max:100',
            'subdistrict_name'  => 'required|string|max:100',
            'zipcode'           => 'nullable|string|max:10',
            'branch_id'         => 'required|exists:branches,id',
            'start_year'        => 'nullable|integer|min:1990|max:' . now()->year,
        ]);

        $user = User::create([
            'line_uid'      => $request->line_uid,
            'name'          => $request->first_name,
            'lastname'      => $request->last_name,
            'phone'         => $request->phone,
            'employee_code' => $request->employee_code ?: 'BA' . time(),
            'birthdate'     => $request->birthday ?: now()->subYears(25)->toDateString(),
            'start_year'    => $request->start_year ?: now()->year,
            'consent_pdpa'  => true,
            'photo_url'     => $request->line_picture_url,
            'level'         => 'gold',
            'is_active'     => true,
        ]);

        UserAddress::create([
            'user_id'          => $user->id,
            'address'          => $request->address_line,
            'province_name'    => $request->province_name,
            'district_name'    => $request->district_name,
            'subdistrict_name' => $request->subdistrict_name,
            'postal_code'      => $request->zipcode,
        ]);

        UserBranch::create([
            'user_id'   => $user->id,
            'branch_id' => $request->branch_id,
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json([
            'token' => $token,
            'user'  => $this->userPayload($user),
        ], 201);
    }

    /**
     * ข้อมูล user ปัจจุบัน
     */
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json($this->userPayload($user));
    }

    private function userPayload(User $user): array
    {
        $user->loadMissing(['currentBranch.branch.zone', 'address']);
        $branch = $user->currentBranch ? $user->currentBranch->branch : null;
        $address = $user->address;
        return [
            'id'            => $user->id,
            'first_name'    => $user->name,
            'last_name'     => $user->lastname,
            'phone'         => $user->phone,
            'employee_code' => $user->employee_code,
            'level'         => $user->level,
            'photo_url'     => $user->photo_url,
            'total_points'   => $user->total_points,
            'receipt_points' => $user->receipt_points,
            'is_active'     => $user->is_active,
            'branch'        => $branch ? ['id' => $branch->id, 'name' => $branch->shop_name, 'zone' => $branch->zone ? $branch->zone->name : ''] : null,
            'address'       => $address ? [
                'name'            => trim(($user->name ?: '') . ' ' . ($user->lastname ?: '')),
                'phone'           => $user->phone,
                'address_line'    => $address->address,
                'province_name'   => $address->province_name,
                'district_name'   => $address->district_name,
                'subdistrict_name'=> $address->subdistrict_name,
                'zipcode'         => $address->postal_code,
            ] : null,
        ];
    }

    private function verifyLineToken(string $accessToken): ?array
    {
        try {
            $response = Http::withToken($accessToken)
                            ->get('https://api.line.me/v2/profile');
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // ignore
        }
        return null;
    }
}
