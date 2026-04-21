<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QACategory;
use App\Models\QAItem;
use Illuminate\Http\Request;

class QAItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = QACategory::with(['items' => function ($q) {
            $q->orderBy('order');
        }])->orderBy('order')->get();

        return view('admin.qa.items', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:qa_categories,id',
            'question'    => 'required|string',
            'answer'      => 'required|string',
            'order'       => 'integer|min:0',
        ]);

        QAItem::create($request->only('category_id', 'question', 'answer', 'order'));
        return back()->with('success', 'เพิ่ม Q&A เรียบร้อย');
    }

    public function update(Request $request, QAItem $qaItem)
    {
        $request->validate([
            'category_id' => 'required|exists:qa_categories,id',
            'question'    => 'required|string',
            'answer'      => 'required|string',
            'order'       => 'integer|min:0',
        ]);

        $qaItem->update($request->only('category_id', 'question', 'answer', 'order'));
        return back()->with('success', 'อัพเดท Q&A เรียบร้อย');
    }

    public function destroy(QAItem $qaItem)
    {
        $qaItem->delete();
        return back()->with('success', 'ลบ Q&A เรียบร้อย');
    }
}
