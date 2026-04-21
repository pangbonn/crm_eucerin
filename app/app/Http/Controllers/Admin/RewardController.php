<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardController extends Controller
{
    public function index()
    {
        $rewards = Reward::orderBy('is_active', 'desc')->orderBy('points_required')->paginate(20);
        return view('admin.rewards.index', compact('rewards'));
    }

    public function create()
    {
        return view('admin.rewards.form', ['reward' => new Reward]);
    }

    public function store(Request $request)
    {
        $data = $this->validateReward($request);
        $data['image'] = $this->uploadImage($request);
        Reward::create($data);
        return redirect()->route('admin.rewards.index')->with('success', 'เพิ่มของรางวัลเรียบร้อย');
    }

    public function edit(Reward $reward)
    {
        return view('admin.rewards.form', compact('reward'));
    }

    public function update(Request $request, Reward $reward)
    {
        $data = $this->validateReward($request);
        if ($request->hasFile('image')) {
            if ($reward->image) Storage::disk('public')->delete($reward->image);
            $data['image'] = $this->uploadImage($request);
        } else {
            unset($data['image']);
        }
        $reward->update($data);
        return redirect()->route('admin.rewards.index')->with('success', 'อัพเดทของรางวัลเรียบร้อย');
    }

    public function destroy(Reward $reward)
    {
        if ($reward->image) Storage::disk('public')->delete($reward->image);
        $reward->delete();
        return back()->with('success', 'ลบของรางวัลเรียบร้อย');
    }

    private function validateReward(Request $request): array
    {
        return $request->validate([
            'name'                      => 'required|string|max:200',
            'description'               => 'nullable|string',
            'points_required'           => 'required|integer|min:0',
            'training_points_required'  => 'required|integer|min:0',
            'stock'                     => 'required|integer|min:0',
            'is_active'                 => 'boolean',
            'image'                     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    private function uploadImage(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('rewards', 'public');
        }
        return null;
    }
}
