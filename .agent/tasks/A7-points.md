# A7 — จัดการคะแนน ✅ DONE

## เสร็จแล้ว
- [x] รายชื่อพนักงาน + คะแนนรวม (sort desc)
- [x] ดูประวัติคะแนนรายคน (paginate 30)
- [x] ปรับคะแนน manual (บวก/ลบ + เหตุผล)
- [x] Ranking Top 10 (filter เดือน/ปี, fallback คำนวณ live ถ้าไม่มี snapshot)
- [x] Export Excel คะแนนทั้งหมด

## Files
- `app/Http/Controllers/Admin/PointController.php`
- `app/Exports/PointsExport.php`
- `resources/views/admin/points/index.blade.php`
- `resources/views/admin/points/show.blade.php`
- `resources/views/admin/points/ranking.blade.php`

## ถัดไป → A8 (ยังไม่ทำ)
