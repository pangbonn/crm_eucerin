# A13 — Product Information (Backoffice + LIFF) ✅ DONE

## Acceptance Criteria
- [x] Backoffice จัดการสินค้า: รูป, ชื่อ, รายละเอียด, สถานะเปิด/ปิด
- [x] มีข้อมูลสินค้าเริ่มต้นผ่าน seeder
- [x] API `GET /api/liff/products` สำหรับ LIFF
- [x] LIFF หน้า `/products` แสดงรายการสินค้า + ค้นหา + popup รายละเอียด

## Backoffice
- Migration: `database/migrations/2026_04_23_120000_create_products_table.php`
- Model: `app/Models/Product.php`
- Controller: `app/Http/Controllers/Admin/ProductController.php`
- Views:
  - `resources/views/admin/products/index.blade.php`
  - `resources/views/admin/products/form.blade.php`
- Routes:
  - `routes/web.php` → `Route::resource('products', ...)->except(['show'])`
- Sidebar menu:
  - `config/adminlte.php` → เพิ่มเมนู "จัดการสินค้า"

## Seeder
- `database/seeders/ProductSeeder.php`
- เพิ่มใน `DatabaseSeeder.php`
- Seed แล้ว: 8 รายการ

## API
- `app/Http/Controllers/Api/ProductController.php`
- `routes/api.php` → `GET /api/liff/products` (auth:api)

## LIFF
- Route: `liff/src/router/index.js` → `/products`
- Home shortcut: `liff/src/views/HomeView.vue`
- Page: `liff/src/views/ProductView.vue`
  - Header fixed
  - Search
  - List พร้อมรูปสินค้า
  - คลิกแล้วขึ้น popup แสดงรายละเอียด

## ถัดไป
- E2E test หน้า `/products` บน LINE จริง
