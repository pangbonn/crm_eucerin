# A7 — จัดการคะแนน ✅ DONE

## เสร็จแล้ว
- [x] รายชื่อพนักงาน + คะแนนสะสม (sort desc)
- [x] ดูประวัติคะแนนรายคน (paginate 30)
  - badge แยกสี: ใบเสร็จ (เขียว) / แลกรางวัล (แดง) / Manual (เหลือง)
- [x] ปรับคะแนน manual (บวก/ลบ + เหตุผล)
- [x] Ranking Top 10 (filter เดือน/ปี, fallback คำนวณ live ถ้าไม่มี snapshot)
- [x] Export Excel คะแนนทั้งหมด
- [x] เพิ่มการแสดง "คะแนนคงเหลือ" ในหน้า `admin/points/{user}`
- [x] ปรับสูตรคะแนนสะสมให้ไม่นับ `source='redemption'` และไม่นับคะแนนติดลบ
- [x] API Ranking (`/api/liff/ranking`) ใช้ alias `ranking_points` กันชนกับ `User::getTotalPointsAttribute()`

## Files
- `app/Http/Controllers/Admin/PointController.php`
- `app/Http/Controllers/Api/PointController.php`
- `app/Models/User.php`
- `app/Exports/PointsExport.php`
- `resources/views/admin/points/index.blade.php`
- `resources/views/admin/points/show.blade.php`
- `resources/views/admin/points/ranking.blade.php`

## ถัดไป → A8 ✅
