<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'     => 'required|string|max:100',
            'site_logo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sidebar_theme' => 'required|string',
            'navbar_theme'  => 'required|string',
            'auth_btn_class'=> 'required|string',
            'stamp_max'     => 'required|integer|min:1|max:100',
            'stamp_points'  => 'required|integer|min:1',
        ]);

        if ($request->hasFile('site_logo')) {
            $old = Setting::get('site_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::where('key', 'site_logo')->update(['value' => $path]);
        }

        $simpleKeys = ['site_name', 'sidebar_theme', 'navbar_theme', 'auth_btn_class'];
        foreach ($simpleKeys as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
        }

        Setting::updateOrCreate(
            ['key' => 'stamp_max'],
            ['value' => $request->stamp_max, 'label' => 'จำนวน Stamp สูงสุด', 'type' => 'number']
        );
        Setting::updateOrCreate(
            ['key' => 'stamp_points'],
            ['value' => $request->stamp_points, 'label' => 'คะแนนต่อ 1 Stamp', 'type' => 'number']
        );

        return back()->with('success', 'บันทึกการตั้งค่าเรียบร้อย');
    }
}
