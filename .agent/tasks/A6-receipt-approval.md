# A6 — อนุมัติใบเสร็จ ✅ DONE

## เสร็จแล้ว
- [x] รายการใบเสร็จ pending / approved / rejected (tabs)
- [x] ดูรูปภาพ (แสดงรูปทุกรูป)
- [x] กรอก sales_amount + SKU rows (dynamic เพิ่มได้)
- [x] อนุมัติ → คำนวณคะแนน (base × level_multiplier) → บันทึก points
- [x] ปฏิเสธ + หมายเหตุ
- [x] `PointCalculationService` คำนวณ server-side ทั้งหมด

## Files
- `app/Http/Controllers/Admin/ReceiptController.php`
- `app/Services/PointCalculationService.php`
- `resources/views/admin/receipts/index.blade.php`
- `resources/views/admin/receipts/show.blade.php`

## ถัดไป → A7 ✅
