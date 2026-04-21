<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('type')->orderByDesc('created_at')->get()->groupBy('type');
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.form', ['banner' => new Banner]);
    }

    public function store(Request $request)
    {
        $data = $this->validateBanner($request);
        $data['image_url'] = $this->uploadImage($request);
        Banner::create($data);
        return redirect()->route('admin.banners.index')->with('success', 'เพิ่ม Banner เรียบร้อย');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $this->validateBanner($request);
        if ($request->hasFile('image_url')) {
            if ($banner->image_url && !filter_var($banner->image_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($banner->image_url);
            }
            $data['image_url'] = $this->uploadImage($request);
        } else {
            unset($data['image_url']);
        }
        $banner->update($data);
        return redirect()->route('admin.banners.index')->with('success', 'อัพเดท Banner เรียบร้อย');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_url && !filter_var($banner->image_url, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($banner->image_url);
        }
        $banner->delete();
        return back()->with('success', 'ลบ Banner เรียบร้อย');
    }

    private function validateBanner(Request $request): array
    {
        return $request->validate([
            'type'            => 'required|in:main,receipt,exam,reward',
            'condition_text'  => 'nullable|string|max:500',
            'link_url'        => 'nullable|url|max:500',
            'is_active'       => 'boolean',
            'display_month'   => 'nullable|integer|between:1,12',
            'display_year'    => 'nullable|integer|min:2020',
            'image_url'       => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    private function uploadImage(Request $request): ?string
    {
        if ($request->hasFile('image_url')) {
            return $request->file('image_url')->store('banners', 'public');
        }
        return null;
    }
}
