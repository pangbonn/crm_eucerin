<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QACategory;

class QAController extends Controller
{
    public function index()
    {
        $categories = QACategory::with(['items' => function ($q) {
            $q->orderBy('order');
        }])
        ->orderBy('order')
        ->get()
        ->map(function ($cat) {
            return [
                'id'    => $cat->id,
                'name'  => $cat->name,
                'items' => $cat->items->map(function ($item) {
                    return [
                        'id'       => $item->id,
                        'question' => $item->question,
                        'answer'   => $item->answer,
                    ];
                }),
            ];
        });

        return response()->json($categories);
    }
}
