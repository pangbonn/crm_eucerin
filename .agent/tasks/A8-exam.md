# A8 — Exam & Training ✅ DONE

## เสร็จแล้ว

### Admin Panel
- [x] รายการ Part (เรียง part_number desc) + withCount questions/results
- [x] เพิ่ม/แก้ไข Part — title, banner_image, vdo_url, vdo_file upload ≤10MB, pre/post points, `questions_per_session`
- [x] Toggle active/inactive Part
- [x] CRUD คำถาม Pre-test + Post-test (4 ตัวเลือก, correct_choice 1-4)
- [x] ดูผลสอบรายพนักงาน (filter part/section, paginate 30)
- [x] ExamQuestionSeeder — 20 pre + 20 post questions ต่อ part (Eucerin-themed)

### API (LIFF)
- [x] `GET /api/liff/exams` — คืน parts พร้อม pre/vdo/post status ต่อ user
- [x] `GET /api/liff/exams/{id}/questions?section=pre|post` — random N จาก 20-question bank
- [x] `POST /api/liff/exams/{id}/submit` — score ด้วย question_id (ไม่ใช่ position), ให้คะแนน % จริง
- [x] `POST /api/liff/exams/{id}/video-progress` — บันทึก % วิดีโอ, ผ่านที่ 100%

### LIFF Frontend
- [x] `/exam` — landing page: fixed banner header, CTA banner (จัดการจาก admin), stamp card
- [x] `/exam/list` — รายการ part แต่ละ part มี 3 rows (pre / video / post)
  - Sequential lock: pre → video → post (ต้องทำตามลำดับ)
  - Back button (←) ใน red header กลับไป /exam
  - Toast แจ้งเมื่อ tap row ที่ยังล็อคอยู่
- [x] Video player: ป้องกัน seek ไปข้างหน้า (JS anti-seek), ต้องดูจบ 100%
- [x] Stamp popup — ขึ้นเมื่อดูจบ 100%: "รับ Stamp 1 ดวง" + ปุ่ม "ถัดไป → post-test"
- [x] Post-test result — ปุ่ม "เสร็จสิ้น" (ไม่มี ทำซ้ำ)
- [x] Part ครบทุกรายการ — แสดง "ทำครบทุกรายการแล้ว 🎉" แทน 3 rows, คลิกไม่ได้อีก

### Banner (exam_cta)
- [x] Admin สร้าง banner type `exam_cta` — มี image, button_text (condition_text), link_url
- [x] API BannerController คืน `button_text` + `link_url` สำหรับ exam_cta

## Files
- `app/Http/Controllers/Admin/ExamController.php`
- `app/Http/Controllers/Admin/ExamQuestionController.php`
- `app/Http/Controllers/Api/ExamController.php`
- `app/Models/ExamPart.php` — เพิ่ม `questions_per_session` ใน fillable
- `database/migrations/2026_04_23_000001_add_questions_per_session_to_exam_parts.php`
- `database/seeders/ExamQuestionSeeder.php`
- `resources/views/admin/exams/` — index, form, questions, results
- `liff/src/views/ExamView.vue` — landing page
- `liff/src/views/ExamListView.vue` — list + video + exam flow
- `liff/src/router/index.js` — เพิ่ม `/exam/list`
- `routes/api.php` — เพิ่ม video-progress endpoint

## ถัดไป → A9 ✅
