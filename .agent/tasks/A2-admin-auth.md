# A2 — Admin Authentication ✅ DONE

## เสร็จแล้ว
- [x] `/admin/login` — form login (AdminLTE style)
- [x] login สำเร็จ → redirect `/admin/dashboard`
- [x] login ผิด → แสดง error
- [x] logout → redirect `/admin/login`
- [x] Middleware `admin.auth` ป้องกันทุก `/admin/*` route
- [x] Guard `admin` ใน `config/auth.php`
- [x] Seeder: admin@eucerin.com / Admin@1234

## Files
- `app/Http/Controllers/Admin/AuthController.php`
- `app/Http/Middleware/AdminAuthenticate.php`
- `resources/views/admin/auth/login.blade.php`

## ถัดไป → A3 ✅
