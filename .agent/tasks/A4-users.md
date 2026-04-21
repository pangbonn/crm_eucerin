# A4 — จัดการพนักงาน ✅ DONE

## เสร็จแล้ว
- [x] รายชื่อพนักงาน (search, filter zone/level/status)
- [x] ดูรายละเอียดรายคน (ประวัติคะแนน + ประวัติแลกรางวัล)
- [x] แก้ไขข้อมูล (ชื่อ, นามสกุล, เบอร์, ระดับ, รหัส)
- [x] ปุ่ม "ลาออก" → `is_active=0`, `resigned_at=now()`
- [x] Import Excel ระดับพนักงาน (employee_code + level)
- [x] Export Excel พนักงานทั้งหมด

## Files
- `app/Http/Controllers/Admin/UserController.php`
- `app/Imports/UserLevelImport.php`
- `app/Exports/UsersExport.php`
- `resources/views/admin/users/index.blade.php`
- `resources/views/admin/users/show.blade.php`
- `resources/views/admin/users/edit.blade.php`

## ถัดไป → A5 ✅
