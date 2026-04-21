# A9 — Rewards & Redemptions ⬜ TODO

## Acceptance Criteria
- [ ] รายการรางวัล (CRUD + toggle active + จัดการ stock)
- [ ] รายการแลกรางวัล pending
- [ ] อนุมัติ → stock-- → status=approved
- [ ] ปฏิเสธ → คืนคะแนน (`PointCalculationService::refundRedemptionPoints`) → status=rejected
- [ ] Export รายการจัดส่ง Excel

## Controllers (สร้างแล้วเป็น stub)
- `app/Http/Controllers/Admin/RewardController.php`
- `app/Http/Controllers/Admin/RedemptionController.php`

## Views ที่ต้องสร้าง
- `resources/views/admin/rewards/index.blade.php`
- `resources/views/admin/rewards/form.blade.php`
- `resources/views/admin/redemptions/index.blade.php`

## ถัดไป → A10
