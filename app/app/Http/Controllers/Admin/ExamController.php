<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPart;
use App\Models\UserExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function index()
    {
        $parts = ExamPart::withCount(['questions', 'results'])->orderByDesc('part_number')->get();
        return view('admin.exams.index', compact('parts'));
    }

    public function create()
    {
        return view('admin.exams.form', ['part' => new ExamPart]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePart($request);
        $data['banner_image'] = $this->uploadBanner($request);
        $data['vdo_path'] = $this->uploadVdo($request);
        ExamPart::create($data);
        return redirect()->route('admin.exams.index')->with('success', 'เพิ่ม Part เรียบร้อย');
    }

    public function edit(ExamPart $exam)
    {
        return view('admin.exams.form', ['part' => $exam]);
    }

    public function update(Request $request, ExamPart $exam)
    {
        $data = $this->validatePart($request);

        if ($request->hasFile('banner_image')) {
            if ($exam->banner_image) Storage::disk('public')->delete($exam->banner_image);
            $data['banner_image'] = $this->uploadBanner($request);
        } else {
            unset($data['banner_image']);
        }

        if ($request->hasFile('vdo_file')) {
            if ($exam->vdo_path) Storage::disk('public')->delete($exam->vdo_path);
            $data['vdo_path'] = $this->uploadVdo($request);
        } else {
            unset($data['vdo_path']);
        }

        $exam->update($data);
        return redirect()->route('admin.exams.index')->with('success', 'อัพเดท Part เรียบร้อย');
    }

    public function destroy(ExamPart $exam)
    {
        if ($exam->banner_image) Storage::disk('public')->delete($exam->banner_image);
        if ($exam->vdo_path) Storage::disk('public')->delete($exam->vdo_path);
        $exam->delete();
        return back()->with('success', 'ลบ Part เรียบร้อย');
    }

    public function results(ExamPart $exam, Request $request)
    {
        $results = UserExamResult::with('user')
            ->where('exam_part_id', $exam->id)
            ->when($request->filled('section'), function ($q) use ($request) {
                $q->where('section', $request->section);
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.exams.results', compact('exam', 'results'));
    }

    private function validatePart(Request $request): array
    {
        return $request->validate([
            'title'            => 'required|string|max:200',
            'part_number'      => 'required|integer|min:1',
            'vdo_url'          => 'nullable|url|max:500',
            'vdo_file'         => 'nullable|file|mimes:mp4,webm,mov|max:10240',
            'pre_test_points'  => 'required|integer|min:0',
            'post_test_points' => 'required|integer|min:0',
            'is_active'        => 'boolean',
            'banner_image'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    private function uploadBanner(Request $request): ?string
    {
        if ($request->hasFile('banner_image')) {
            return $request->file('banner_image')->store('banners/exams', 'public');
        }
        return null;
    }

    private function uploadVdo(Request $request): ?string
    {
        if ($request->hasFile('vdo_file')) {
            return $request->file('vdo_file')->store('exams/videos', 'public');
        }
        return null;
    }
}
