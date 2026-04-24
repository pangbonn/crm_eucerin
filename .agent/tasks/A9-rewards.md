# A9 — Rewards & Redemptions ✅ DONE

## Acceptance Criteria
- [x] รายการรางวัล (CRUD + toggle active + จัดการ stock)
- [x] รายการแลกรางวัล pending
- [x] อนุมัติ → status=approved (ไม่ตัดคะแนนซ้ำที่ฝั่ง Admin)
- [x] ปฏิเสธ → คืนคะแนน (`PointCalculationService::refundRedemptionPoints`) → status=rejected
- [x] Export รายการจัดส่ง Excel
- [x] Approved tab สามารถใส่/แก้ไข `ขนส่ง` + `เลข Tracking` ได้

## Admin Panel
- `Admin/RewardController.php` — CRUD + image upload (storage/public/rewards/)
- `Admin/RedemptionController.php` — approve/reject + update tracking
- `admin/rewards/index.blade.php` — ตารางพร้อม thumbnail, stock alert สีแดง
- `admin/rewards/form.blade.php` — ฟอร์ม + image preview
- `admin/redemptions/index.blade.php` — tabs pending/approved/rejected + reject modal + tracking modal + export

## Redemption Flow Update (2026-04-23)
- User กดแลกใน LIFF → ตัดคะแนนทันที (source=`redemption`) + เก็บ snapshot ที่อยู่จัดส่งต่อ 1 redemption
- Admin อนุมัติ → อัปเดตสถานะอย่างเดียว (ไม่ตัดคะแนนรอบสอง)
- Admin สามารถอัปเดต `shipping_carrier` และ `tracking_number` ได้ภายหลังในแท็บ approved
- เพิ่ม migration: `2026_04_23_180000_add_shipping_meta_to_reward_redemptions_table.php`

## LIFF Frontend (2026-04-23)
- `liff/src/views/RewardsView.vue` — ปรับ UI:
  - Banner แทน header (ดึงจาก `/api/liff/banner/reward`)
  - Points bar ใต้ banner
  - Label "รายการของรางวัล" + list layout (card-side แนวตั้ง)
  - Dialog แยกเป็นหลายขั้น: ของรางวัล → ที่อยู่จัดส่ง (แบบ Register) → สรุป → ยืนยันซ้ำ
  - เมื่อแลกสำเร็จมี Success Dialog

## Image URL Fix (2026-04-23)
- `vite.config.js` — เพิ่ม proxy `/storage` → Laravel (port 8000)
- API controllers ใช้ `Storage::url()` (relative `/storage/...`) — Vite proxy จัดการให้
- `TrustProxies.php` — `$proxies = '*'` สำหรับ ngrok

## ถัดไป → A10
