<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function show(string $type)
    {
        $allowed = ['main', 'receipt', 'receipt_cta', 'exam', 'exam_cta', 'reward'];
        if (!in_array($type, $allowed)) {
            return response()->json(null);
        }

        $banner = Banner::where('type', $type)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('display_month')
                  ->orWhere('display_month', now()->month);
            })
            ->where(function ($q) {
                $q->whereNull('display_year')
                  ->orWhere('display_year', now()->year);
            })
            ->latest()
            ->first();

        if (!$banner) {
            return response()->json(null);
        }

        $imageUrl = $banner->image_url;
        if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $imageUrl = Storage::url($imageUrl);
        }

        return response()->json([
            'id'          => $banner->id,
            'type'        => $banner->type,
            'image_url'   => $imageUrl,
            'button_text' => $banner->condition_text,
            'link_url'    => $banner->link_url,
        ]);
    }
}
