# A10 — จัดการ Banner ⬜ TODO

## Acceptance Criteria
- [ ] รายการ Banner แยก type (main/receipt/exam/reward)
- [ ] อัพโหลดรูป Banner ใหม่ → เก็บใน `storage/app/private/banners/`
- [ ] ตั้งค่า condition_text + display_month/year
- [ ] Toggle active (แต่ละ type ควร active 1 อัน)

## Controllers (stub)
- `app/Http/Controllers/Admin/BannerController.php`

## Views ที่ต้องสร้าง
- `resources/views/admin/banners/index.blade.php`
- `resources/views/admin/banners/form.blade.php`

## ถัดไป → A11
