# A11 — Q&A ✅ DONE

## Acceptance Criteria
- [x] CRUD หมวดหมู่ (ชื่อ + order)
- [x] CRUD Q&A รายข้อ (question, answer, category, order)
- [x] API `GET /api/liff/qa` คืนข้อมูลแบบ grouped by category
- [x] LIFF หน้า `/qa` แสดงรายการคำถาม + search + เปิดดูคำตอบได้

## Admin Panel
- `app/Http/Controllers/Admin/QACategoryController.php`
- `app/Http/Controllers/Admin/QAItemController.php`
- `resources/views/admin/qa/categories.blade.php`
- `resources/views/admin/qa/items.blade.php`

## API
- `app/Http/Controllers/Api/QAController.php`
- `routes/api.php` → `GET /api/liff/qa` (auth:api)

## Seeder (2026-04-23)
- `database/seeders/QASeeder.php`
- เพิ่มใน `DatabaseSeeder.php`
- ข้อมูลตัวอย่าง: 4 หมวด / 12 รายการ

## LIFF (2026-04-23)
- `liff/src/views/QAView.vue`
  - Header fixed
  - Search คำถาม
  - กดเปิด/ปิดคำตอบได้
  - Section header พื้นแดง ตัวหนังสือขาว
  - ไม่มี bottom menu ตาม requirement

## ถัดไป → A12 ✅
