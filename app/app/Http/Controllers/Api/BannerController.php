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

        $banner = $this->getActiveBanner($type);

        if (!$banner) {
            return response()->json(null);
        }

        $data = [
            'id'          => $banner->id,
            'type'        => $banner->type,
            'image_url'   => $this->toFullUrl($banner->image_url),
            'button_text' => $banner->condition_text,
            'link_url'    => $banner->link_url,
        ];

        // เมื่อดึง receipt banner ให้รวม button background จาก receipt_cta มาด้วย
        if ($type === 'receipt') {
            $cta = $this->getActiveBanner('receipt_cta');
            $data['button_bg_url'] = $cta ? $this->toFullUrl($cta->image_url) : null;
        }

        return response()->json($data);
    }

    private function getActiveBanner(string $type): ?Banner
    {
        return Banner::where('type', $type)
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
    }

    private function toFullUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        return url(Storage::url($path));
    }
}
