# CRM Eucerin — Agent Entry Point

## Project
CRM สำหรับ Beauty Advisor แบรนด์ Eucerin ผ่าน LINE OA  
Client: Activation Hub Co., Ltd.

## Stack
- Laravel 8.x (PHP 7.4) + AdminLTE 3 — Admin Panel
- Vue 3 + DaisyUI — LINE LIFF Frontend
- MariaDB — Database

## อ่านก่อนทำงาน (ตามลำดับ)
1. `.agent/STATUS.md` — งานปัจจุบัน + งานถัดไป (อ่านทุกครั้ง)
2. `.agent/tasks/[TASK_ID].md` — spec งานที่จะทำ
3. `project-info.md` — full spec (อ่านเฉพาะเมื่อต้องการรายละเอียด)

## โครงสร้าง Laravel (หลัง setup)
```
app/Http/Controllers/Admin/   — Admin controllers
app/Http/Controllers/Api/     — API controllers (LIFF)
app/Models/                   — Eloquent models
database/migrations/          — DB migrations
resources/views/admin/        — AdminLTE Blade views
routes/web.php                — Admin routes
routes/api.php                — API routes
```

## Convention สำคัญ
- Admin auth: `auth:admin` guard (แยกจาก user)
- API auth: JWT (tymon/jwt-auth)
- File upload: `storage/app/private/receipts/` (ไม่ใช่ public)
- คะแนนคำนวณ server-side เสมอ — ห้าม trust client
- ทุก API ต้อง check ownership ก่อน return ข้อมูล


## คำสั่ง Dev
```bash
php artisan serve
php artisan migrate:fresh --seed
php artisan storage:link
```
