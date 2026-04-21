<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QACategory;
use Illuminate\Http\Request;

class QACategoryController extends Controller
{
    public function index()
    {
        $categories = QACategory::withCount('items')->orderBy('order')->get();
        return view('admin.qa.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100', 'order' => 'integer|min:0']);
        QACategory::create(['name' => $request->name, 'order' => $request->order ?? 0]);
        return back()->with('success', 'เพิ่มหมวดหมู่เรียบร้อย');
    }

    public function update(Request $request, QACategory $qaCategory)
    {
        $request->validate(['name' => 'required|string|max:100', 'order' => 'integer|min:0']);
        $qaCategory->update(['name' => $request->name, 'order' => $request->order ?? 0]);
        return back()->with('success', 'อัพเดทหมวดหมู่เรียบร้อย');
    }

    public function destroy(QACategory $qaCategory)
    {
        $qaCategory->delete();
        return back()->with('success', 'ลบหมวดหมู่เรียบร้อย');
    }
}
