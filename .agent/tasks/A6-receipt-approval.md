# A6 — อนุมัติใบเสร็จ ✅ DONE

## เสร็จแล้ว
- [x] รายการใบเสร็จ pending / approved / rejected / cancelled (tabs)
- [x] ดูรูปภาพ (แสดงรูปทุกรูป ผ่าน private storage)
- [x] กรอก sales_amount + SKU rows (dynamic เพิ่มได้)
- [x] อนุมัติ → คำนวณคะแนน (base × level_multiplier) → บันทึก points
- [x] ปฏิเสธ + หมายเหตุ
- [x] `PointCalculationService` คำนวณ server-side ทั้งหมด
- [x] Re-approve (แก้ไขคะแนน) — reverse คะแนนเดิม + ออกคะแนนใหม่
- [x] Cancel approved receipt — status='cancelled' + หักคะแนนคืน + เก็บ note
  - migration: เพิ่ม 'cancelled' ใน ENUM status
- [x] Point history per receipt — `Receipt::pointHistory()` แสดง timeline บน show page
- [x] SKU table — แสดง sku/qty/คะแนนต่อชิ้น/รวม พร้อม footer รวม
- [x] Form pre-fill — เมื่อ re-approve จะเติม sales_amount + SKU เดิมให้อัตโนมัติ

## Files
- `app/Http/Controllers/Admin/ReceiptController.php`
- `app/Services/PointCalculationService.php`
- `app/Models/Receipt.php` — เพิ่ม `pointHistory()` relationship
- `resources/views/admin/receipts/index.blade.php`
- `resources/views/admin/receipts/show.blade.php`
- `database/migrations/2026_04_22_140801_add_cancelled_status_to_receipts_table.php`

## ถัดไป → A7 ✅
