<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Province;
use App\Models\District;
use App\Models\Subdistrict;

class LocationController extends Controller
{
    public function provinces()
    {
        return response()->json(
            Province::orderBy('name_th')->get(['id', 'name_th'])
        );
    }

    public function districts($provinceId)
    {
        return response()->json(
            District::where('province_id', $provinceId)->orderBy('name_th')->get(['id', 'name_th'])
        );
    }

    public function subdistricts($districtId)
    {
        return response()->json(
            Subdistrict::where('district_id', $districtId)->orderBy('name_th')->get(['id', 'name_th', 'postal_code'])
        );
    }

    public function branches()
    {
        return response()->json(
            Branch::with(['zone', 'shopType', 'province'])
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->orderBy('shop_name')
                ->get()
                ->map(function ($b) {
                    return [
                        'id'            => $b->id,
                        'name'          => $b->shop_name,
                        'zone'          => $b->zone ? $b->zone->name : '',
                        'zone_id'       => $b->zone_id,
                        'shop_type'     => $b->shopType ? $b->shopType->name : '',
                        'shop_type_id'  => $b->shop_type_id,
                        'province_id'   => $b->province_id,
                        'province_name' => $b->province ? $b->province->name_th : '',
                    ];
                })
        );
    }

    public function zones()
    {
        return response()->json(
            \App\Models\Zone::orderBy('name')->get(['id', 'code', 'name'])
        );
    }

    public function shopTypes()
    {
        return response()->json(
            \App\Models\ShopType::orderBy('name')->get(['id', 'name'])
        );
    }

    public function stampConfig()
    {
        return response()->json([
            'stamp_max'    => (int) \App\Models\Setting::get('stamp_max', 8),
            'stamp_points' => (int) \App\Models\Setting::get('stamp_points', 10),
        ]);
    }
}
