# Agent Status — CRM Eucerin

> อัพเดทไฟล์นี้ทุกครั้งก่อนจบ session

## Last Updated
2026-04-23

## Phase ปัจจุบัน
**Phase 4: LIFF Frontend — Rewards/Points/Profile polish (เสร็จแล้ว)**

## งานที่เสร็จแล้ว
- [x] A1: Project Setup — Laravel 8, migrations, models, guards, routes, AdminLTE
- [x] A2: Admin Authentication — login/logout, middleware `admin.auth`, seeder
- [x] A3: Dashboard — widgets stats, ตารางพนักงานใหม่, ใบเสร็จรอตรวจสอบ
- [x] A4: จัดการพนักงาน — index/show/edit/resign/import-level/export
- [x] A5: จัดการสาขา — CRUD + filter zone/shop_type
- [x] A6: อนุมัติใบเสร็จ — approve+SKU / reject + PointCalculationService
  - Re-approve: reverse คะแนนเดิม → ออกคะแนนใหม่
  - Cancel approved: status='cancelled' + หักคะแนนคืน
  - Double-cancel race condition fix: `DB::transaction` + `lockForUpdate()`
  - Level multiplier: Platinum 2×, Silver 1.5×, Gold 1× (server-side)
  - Branch edit SQL fix: join zones table แทน `orderBy('zone')`
- [x] A7: จัดการคะแนน — index/show/adjust/ranking/export
- [x] A8: Exam & Training — ✅ ครบ
  - Admin CRUD Part + คำถาม Pre/Post + ดูผลสอบ
  - API: index / questions (random 10/20) / submit (score by question_id) / video-progress
  - LIFF `/exam`: landing — fixed banner, CTA banner (admin-managed), stamp card
  - LIFF `/exam/list`: 3 rows/part (pre→video→post), sequential lock, back button
  - Video: ป้องกัน seek, ต้องดู 100%, Stamp popup → ถัดไป → post-test
  - Completed part: ล็อคถาวร "ทำครบทุกรายการแล้ว 🎉"
  - Post-test: ปุ่ม "เสร็จสิ้น"
- [x] A9: Rewards & Redemptions — RewardController, RedemptionController, views
- [x] A10: Banner Management — exam_cta / receipt_cta type, link_url, button_text
- [x] A10b: Banner — full URL response, button_bg_url field (receipt type), CMS อัปโหลดรูปปุ่ม
- [x] A11: Q&A Management — QACategoryController, QAItemController, views
- [x] A12: Reports/Export — ReportController + 4 Export classes + reports/index view
- [x] A13: Product Information (Backoffice + LIFF) — products CRUD, seeder, API, `/products` page + popup detail

- [x] API Auth — JWT expiry → re-login ผ่าน LINE (ไม่ดีดไป /register)
- [x] App.vue redirect: รองรับ deep-link (`redirect` query) ไม่เด้งทับ path ปลายทาง
- [x] API Layer — LocationController เพิ่ม zones, shop-types endpoints
- [x] DB Normalization — แยก zones, shop_types ออกเป็นตารางใหม่ (FK จาก branches)
- [x] BranchSeeder — 61 mock branches ครอบคลุม BKK1-9, EAST, NEL, NEU, NU, SOUTH, DS
- [x] ProvinceSeeder — 77 จังหวัด / 930 อำเภอ / 7452 ตำบล จาก JSON files
- [x] Settings Module — DB-driven logo/สี Sidebar/สี Navbar/สีปุ่ม Login
- [x] Image/Video Preview — ทุกฟอร์มที่มี file input
- [x] LIFF RegisterView — 4 Steps (ข้อมูล / ที่อยู่ / สาขา / เสร็จ)
- [x] AddressTypeahead — bug fix isFocused + case-insensitive search
- [x] DB + phpMyAdmin — local MariaDB ผ่าน socket
- [x] QA Seeder — 4 หมวด / 12 รายการ
- [x] Product Seeder — 8 รายการ
- [x] LIFF `/qa` — fixed header, no bottom menu, section header สีแดง, toggle answer
- [x] LIFF `/products` — list + image + search + popup รายละเอียด
- [x] LIFF `/rewards` — multi-step redeem dialogs, ที่อยู่แบบ Register, success dialog
- [x] LIFF `/profile` — ตัด section แต้ม, history แยกรอบเดือน, กดดูที่อยู่+tracking ได้
- [x] LIFF logout — กดออกจากระบบแล้วเด้งไป LINE login
- [x] Admin `/redemptions?tab=approved` — เพิ่มฟอร์มใส่/แก้ tracking ได้
- [x] คะแนนสะสม `/api/liff/me` + `/api/liff/ranking` — คิดแบบไม่หัก redemption และกันชน accessor ชื่อฟิลด์
- [x] Admin `/points` และ `/points/{id}` — แสดงคะแนนสะสม + คะแนนคงเหลือแยกกัน

## งานถัดไป (Next)
1. ทดสอบ E2E exam flow บน ngrok + LINE จริง — ตรวจ anti-seek, stamp popup, completed lock
2. ทดสอบ receipt re-approve + point history บน localhost
3. ทดสอบ Register flow จนครบ — Step 3 branch search
4. Deploy liff/dist → server eucerin-app.bsworkup.com/liff
5. ตั้งค่า Nginx SPA fallback บน server
6. ทดสอบ E2E `/qa` และ `/products` บน LINE จริง (deep-link + popup + image load)
7. ทดสอบ E2E `/rewards` และ `/profile` บน LINE จริง (redeem flow + tracking detail + logout/login)

## DB / Dev
- `php artisan serve` จาก `/crm_eucerin/app/`
- `npm run dev` จาก `/crm_eucerin/liff/`
- ngrok: `ngrok http 5173` (LIFF port)
- phpMyAdmin: http://127.0.0.1:8080 (dbadmin / example)
- Admin panel: http://localhost:8000/admin/login → admin@eucerin.com /  Admin@1234 
- DB: crm_eucerin (local MariaDB, DB_HOST=localhost, DB_USERNAME=bsmackbook, DB_PASSWORD=)

## PHP 7.4 Gotchas — ทำผิดแล้ว 3 ครั้ง อย่าทำซ้ำ
- ❌ `match()` → ใช้ `switch` แทน
- ❌ Constructor property promotion `__construct(private Foo $x)` → ประกาศ property แยก
- ❌ Nullsafe operator `?->` → ใช้ `$x ? $x->y : null` หรือ `isset()` แทน
- ❌ Named arguments `fn(foo: $x)` → ไม่รองรับ
