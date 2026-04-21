# Agent Status — CRM Eucerin

> อัพเดทไฟล์นี้ทุกครั้งก่อนจบ session

## Last Updated
2026-04-20

## Phase ปัจจุบัน
**Phase 2: LIFF Frontend (Vue.js 3 + DaisyUI)**

## งานที่เสร็จแล้ว
- [x] A1: Project Setup — Laravel 8, migrations, models, guards, routes, AdminLTE
- [x] A2: Admin Authentication — login/logout, middleware `admin.auth`, seeder
- [x] A3: Dashboard — widgets stats, ตารางพนักงานใหม่, ใบเสร็จรอตรวจสอบ
- [x] A4: จัดการพนักงาน — index/show/edit/resign/import-level/export
- [x] A5: จัดการสาขา — CRUD + filter zone/shop_type
- [x] A6: อนุมัติใบเสร็จ — approve+SKU / reject + PointCalculationService
- [x] A7: จัดการคะแนน — index/show/adjust/ranking/export
- [x] A8: Exam & Training — ExamController, ExamQuestionController, views; รองรับ VDO อัพโหลดไฟล์ ≤10MB (vdo_path) + URL
- [x] A9: Rewards & Redemptions — RewardController, RedemptionController, views
- [x] A10: Banner Management — BannerController, views (index/form)
- [x] A11: Q&A Management — QACategoryController, QAItemController, views
- [x] A12: Reports/Export — ReportController + 4 Export classes + reports/index view

- [x] API Layer — 6 controllers, 22 endpoints (/api/liff/*), JWT auth ทำงาน, ทดสอบผ่าน
  - AuthController (login, register, me)
  - LocationController (provinces, districts, subdistricts, branches)
  - ReceiptController (store + MIME check)
  - PointController (index paginated, ranking)
  - ExamController (index, questions, submit, myResults)
  - RewardController (index, redeem+transaction, myRedemptions)
  - BannerController (show by type)
  - QAController (index grouped)
- [x] LIFF web route (/liff/*) → Vue SPA blade

- [x] แยก LIFF frontend ออกเป็น standalone Vite project `/crm/liff/`
- [x] Mock LIFF mode (VITE_MOCK_LIFF=true) ทดสอบโดยไม่ต้อง LINE จริง
- [x] Vite proxy `/api` → Laravel :8000
- [x] ProfileView, QAView ครบ — ทุก views F1–F8 สำเร็จ
- [x] Build สำเร็จ, Dev server :5173 ทำงาน

## งานถัดไป (Next)
1. ทดสอบ E2E บน browser: http://localhost:5173
   - Register flow (mock LINE → สร้าง user จริงใน DB)
   - Home, Points, Exam, Rewards, Profile, QA
2. ตั้งค่า LINE_LIFF_ID ใน `.env.production` + deploy
3. `php artisan storage:link` สำหรับ uploaded files

## PHP 7.4 Gotchas — ทำผิดแล้ว 3 ครั้ง อย่าทำซ้ำ
- ❌ `match()` → ใช้ `switch` แทน
- ❌ Constructor property promotion `__construct(private Foo $x)` → ประกาศ property แยก
- ❌ Nullsafe operator `?->` → ใช้ `$x ? $x->y : null` หรือ `isset()` แทน
- ❌ Named arguments `fn(foo: $x)` → ไม่รองรับ

## Dev
- URL: http://localhost:8000/admin/login
- Login: admin@eucerin.com / Admin@1234
- `php artisan serve` จาก `/crm/app/`
- DB: crm_eucerin (MariaDB)
