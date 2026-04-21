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
            Subdistrict::where('district_id', $districtId)->orderBy('name_th')->get(['id', 'name_th', 'zipcode'])
        );
    }

    public function branches()
    {
        return response()->json(
            Branch::where('is_active', true)
                ->whereNull('deleted_at')
                ->orderBy('shop_name')
                ->get(['id', 'shop_name as name', 'zone', 'shop_type'])
        );
    }
}
