# A8 — Exam & Training ⬜ TODO

## เป้าหมาย
Admin สร้าง/แก้ไข Part, คำถาม Pre/Post, ตั้งค่า VDO URL

## Acceptance Criteria
- [ ] รายการ Part (เรียง part_number desc)
- [ ] เพิ่ม/แก้ไข Part (title, banner_image upload, vdo_url, pre/post points)
- [ ] CRUD คำถาม Pre-test (4 ตัวเลือก, กำหนด correct_choice 1-4)
- [ ] CRUD คำถาม Post-test
- [ ] ดูผลสอบรายพนักงาน (filter part/section)
- [ ] Toggle active/inactive Part

## Controllers (สร้างแล้วเป็น stub)
- `app/Http/Controllers/Admin/ExamController.php`
- `app/Http/Controllers/Admin/ExamQuestionController.php`

## Views ที่ต้องสร้าง
- `resources/views/admin/exams/index.blade.php`
- `resources/views/admin/exams/form.blade.php`
- `resources/views/admin/exams/questions.blade.php`
- `resources/views/admin/exams/results.blade.php`

## ถัดไป → A9
