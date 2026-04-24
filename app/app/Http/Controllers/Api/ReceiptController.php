<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'images'   => 'required|array|min:1|max:5',
            'images.*' => 'file|mimes:jpg,jpeg,png|max:1024',
        ]);

        $user  = $request->user();
        $paths = [];

        foreach ($request->file('images') as $file) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file->getRealPath());
            finfo_close($finfo);
            if (!in_array($mime, ['image/jpeg', 'image/png'])) {
                return response()->json(['message' => 'ประเภทไฟล์ไม่ถูกต้อง (รองรับ JPG, PNG เท่านั้น)'], 422);
            }
            $paths[] = $file->store('receipts/' . $user->id, 'private');
        }

        $receipt = Receipt::create([
            'user_id'  => $user->id,
            'images'   => $paths,
            'status'   => 'pending',
        ]);

        return response()->json(['message' => 'ส่งใบเสร็จเรียบร้อย รอการตรวจสอบ', 'id' => $receipt->id], 201);
    }
}
