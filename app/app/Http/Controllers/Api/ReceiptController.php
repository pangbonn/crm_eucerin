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
            'image'         => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'purchase_date' => 'nullable|date',
            'total_amount'  => 'nullable|numeric|min:0',
            'shop_name'     => 'nullable|string|max:200',
            'skus'          => 'nullable|string', // JSON string from FormData
        ]);

        $user = $request->user();

        // ตรวจ MIME จริงด้วย finfo
        $file = $request->file('image');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);
        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            return response()->json(['message' => 'ประเภทไฟล์ไม่ถูกต้อง'], 422);
        }

        $path = $file->store('receipts/' . $user->id, 'private');

        $skuData = [];
        if ($request->filled('skus')) {
            $decoded = json_decode($request->skus, true);
            if (is_array($decoded)) {
                $skuData = $decoded;
            }
        }

        $receipt = Receipt::create([
            'user_id'       => $user->id,
            'images'        => [$path],
            'sales_amount'  => $request->total_amount,
            'sku_data'      => $skuData,
            'status'        => 'pending',
        ]);

        return response()->json(['message' => 'ส่งใบเสร็จเรียบร้อย รอการตรวจสอบ', 'id' => $receipt->id], 201);
    }
}
