# CRM - Eucerin Beauty Advisor Loyalty System

> ระบบ CRM สำหรับพนักงานขาย/Beauty Advisor แบรนด์ Eucerin  
> พัฒนาโดย Activation Hub Co., Ltd.  
> ช่องทาง: LINE OA (BT Eucerin)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 8.x (PHP 7.4) |
| Admin Panel | AdminLTE 3.x |
| Frontend (User) | Vue.js 3 + DaisyUI (Tailwind CSS) |
| Database | MariaDB |
| Platform | LINE OA (LIFF / Messaging API) |
| Auth | admin guard (session) + JWT (tymon/jwt-auth) for API |
| File Upload | Storage::disk('public') — storage link required |
| Excel | maatwebsite/excel ^3.1 |

---

## Feature List — สถานะงาน

> สัญลักษณ์: `[ ]` = ยังไม่ทำ · `[x]` = เสร็จแล้ว · `[-]` = กำลังทำ

---

### Phase 1: Admin Panel ✅ เสร็จทั้งหมด

#### A1 — Project Setup `[x]`
- [x] สร้าง Laravel 8 project (PHP 7.4)
- [x] ติดตั้ง AdminLTE 3 + dependencies
- [x] Config `.env` + เชื่อม MariaDB
- [x] สร้าง Database migrations ครบทุกตาราง (8 migration files)
- [x] Seed ข้อมูลเริ่มต้น (Admin, Provinces, Branches)

#### A2 — Admin Authentication `[x]`
- [x] Login / Logout Admin (email + password)
- [x] Middleware `admin.auth` ป้องกัน Admin routes
- [x] Admin user seeder — admin@eucerin.com / Admin@1234

#### A3 — Dashboard `[x]`
- [x] สรุปจำนวนพนักงาน / ใบเสร็จรอตรวจสอบ / รางวัลรอ / คะแนนรวม
- [x] ตารางพนักงานใหม่ + ใบเสร็จรอตรวจสอบ

#### A4 — จัดการพนักงาน `[x]`
- [x] รายชื่อพนักงาน (filter เขต/สาขา/ระดับ)
- [x] ดูรายละเอียด / แก้ไขข้อมูล
- [x] กดปุ่ม "ลาออก" → `is_active = 0`
- [x] Import ระดับพนักงาน (Excel: employee_code, level)
- [x] Export รายชื่อพนักงาน Excel

#### A5 — จัดการสาขา `[x]`
- [x] CRUD สาขา (filter zone/shop_type)
- [x] Soft delete

#### A6 — อนุมัติใบเสร็จ `[x]`
- [x] รายการใบเสร็จ (tabs: pending / approved / rejected)
- [x] ดูรูปภาพ + กรอกยอด + SKU rows
- [x] อนุมัติ → คำนวณคะแนน Auto (PointCalculationService × level multiplier)
- [x] ปฏิเสธ + หมายเหตุ

#### A7 — จัดการคะแนน `[x]`
- [x] คะแนนรายคน + history
- [x] ปรับคะแนน Manual + เหตุผล
- [x] Ranking Top 10
- [x] Export Excel

#### A8 — Exam & Training `[x]`
- [x] CRUD Part (ชื่อ, Banner, VDO URL หรืออัพโหลดไฟล์ ≤ 10MB)
- [x] จัดการคำถาม Pre/Post test (CRUD)
- [x] ดูผลสอบรายพนักงาน
- [x] Toggle เปิด/ปิด Part

#### A9 — ของรางวัล & แลกรางวัล `[x]`
- [x] CRUD รางวัล (รูป, คะแนน, stock)
- [x] อนุมัติ/ปฏิเสธการแลก (stock--, refund points)

#### A10 — Banner `[x]`
- [x] CRUD Banner ทุกประเภท (main/receipt/exam/reward)
- [x] Toggle active/inactive + filter เดือน/ปี

#### A11 — Q&A `[x]`
- [x] CRUD หมวดหมู่ Q&A
- [x] CRUD Q&A รายข้อ (จัดกลุ่มตามหมวด)

#### A12 — รายงาน / Export `[x]`
- [x] Export พนักงาน / คะแนน / แลกรางวัล (filter เดือน/ปี) / ผลสอบ

---

### Phase 2: LINE LIFF Frontend (Vue.js 3 + DaisyUI) `[-]`

#### F1 — Setup & Auth `[-]`
- [x] Vue 3 + Laravel Mix + DaisyUI + Tailwind CSS setup
- [x] Pinia store + Vue Router
- [x] Auth store (initLiff, loginWithLine, fetchUser)
- [x] Router guard (requiresAuth → redirect /liff/register)
- [ ] ติดตั้ง LINE LIFF SDK จริง + ตั้งค่า LIFF_ID

#### F2 — Register Flow `[x]`
- [x] Step 1: ชื่อ/นามสกุล/เบอร์/รหัสพนักงาน/วันเกิด
- [x] Step 2: ที่อยู่ (cascade Province → District → Subdistrict)
- [x] Step 3: เลือกสาขา (search + scroll)
- [x] Step 4: หน้ายืนยันเสร็จสิ้น
- [x] Validation client-side ทุก step

#### F3 — หน้าหลัก + Bottom Nav `[x]`
- [x] Bottom Navigation 5 tabs
- [x] Banner กิจกรรม + User card (ชื่อ/ระดับ/คะแนน)
- [x] Quick action grid (ส่งใบเสร็จ / Exam / แลกรางวัล / Q&A)

#### F4 — สะสมคะแนน `[x]`
- [x] PointsView: ประวัติคะแนน + pagination
- [x] ReceiptView: อัพโหลดรูปใบเสร็จ + SKU rows
- [x] Preview รูป + validation ขนาดไม่เกิน 5MB

#### F5 — Exam & Training `[x]`
- [x] รายการ Part + EC Passport stamps
- [x] Section selector + quiz (MCQ 4 ตัวเลือก)
- [x] VDO embed (iframe) + ผลสอบ passed/failed
- [x] Submit ผลสอบ → รับ stamp → อัพเดท user

#### F6 — แลกรางวัล `[x]`
- [x] รายการรางวัล grid + คะแนนเทียบ
- [x] Modal ยืนยันแลก
- [x] ประวัติการแลก (tabs)

#### F7 — โปรไฟล์ `[ ]`
- [ ] ProfileView: ข้อมูลส่วนตัว + ประวัติแลก

#### F8 — Q&A `[ ]`
- [ ] QAView: จัดกลุ่มตามหมวด + ค้นหา

---

### Phase 3: API Layer (Laravel) `[ ]`

> API controllers ยังไม่ได้สร้าง — ต้องทำก่อน LIFF ใช้งานจริงได้

- [ ] `POST /api/liff/login` — แลก LINE token → JWT
- [ ] `GET  /api/liff/me` — user profile
- [ ] `POST /api/liff/register` — ลงทะเบียนพนักงาน
- [ ] `GET  /api/liff/provinces` / districts / subdistricts / branches
- [ ] `POST /api/liff/receipts` — ส่งใบเสร็จ (multipart)
- [ ] `GET  /api/liff/points` — ประวัติคะแนน (paginated)
- [ ] `GET  /api/liff/exams` — รายการ Part
- [ ] `GET  /api/liff/exams/{id}/questions?section=pre|post`
- [ ] `POST /api/liff/exams/{id}/submit` — ส่งคำตอบ
- [ ] `GET  /api/liff/rewards` — รายการรางวัล
- [ ] `POST /api/liff/redeem` — แลกรางวัล
- [ ] `GET  /api/liff/redemptions` — ประวัติแลก (user)
- [ ] `GET  /api/liff/banner/{type}` — banner ตามประเภท
- [ ] `GET  /api/liff/exam-results` — stamp/ผลสอบของ user
- [ ] `GET  /api/liff/qa` — Q&A จัดกลุ่ม

---

### Phase 4: Security & Production `[ ]`

- [ ] Validate LINE `access_token` กับ LINE server (/oauth2/v2.1/verify)
- [ ] File upload: ตรวจ MIME จริงด้วย `finfo` + เก็บนอก public
- [ ] IDOR protection ทุก API endpoint (ownership check)
- [ ] Rate limiting (login, receipt upload)
- [ ] VDO completion: backend validate ว่าไม่เคยรับ stamp part นี้มาก่อน
- [ ] HTTPS enforced

---

## Architecture Overview

```
LINE OA (BT Eucerin)
    └── LIFF App (Vue.js 3 + DaisyUI)    ← resources/js/liff/
            └── Laravel API (/api/liff/*)
                    └── MariaDB

Admin Panel
    └── AdminLTE (Blade views)            ← resources/views/admin/
            └── Laravel Web Routes (/admin/*)
                    └── MariaDB
```

---

## Database Schema (MariaDB)

### `admins`
```sql
id, name, email, password, created_at, updated_at
```

### `users` — พนักงาน (Beauty Advisor)
```sql
id, line_uid (unique), first_name, last_name, phone, birthday,
employee_code, level (enum: gold,silver,platinum),
is_active (tinyint), line_display_name, line_picture_url,
created_at, updated_at
```

### `user_addresses`
```sql
id, user_id, address_line, province_id, district_id, subdistrict_id, zipcode
```

### `provinces` / `districts` / `subdistricts`
```sql
provinces: id, name_th, name_en
districts: id, province_id, name_th
subdistricts: id, district_id, name_th, zipcode
```

### `branches`
```sql
id, name, zone (enum: 19 zones), shop_type, address,
province_id, is_active, deleted_at, created_at, updated_at
```

### `user_branches`
```sql
id, user_id, branch_id, assigned_at
```

### `points`
```sql
id, user_id, points (signed int), type (enum: receipt,exam,adjust,redeem,refund),
reference_id, note, created_at
```

### `receipts`
```sql
id, user_id, image_path, purchase_date, total_amount, shop_name,
sku_data (json), status (enum: pending,approved,rejected),
approved_by, approved_at, points_awarded, reject_reason, created_at, updated_at
```

### `exam_parts`
```sql
id, title, part_number, banner_image, vdo_url (external URL),
vdo_path (uploaded file, ≤10MB, stored in public disk), 
pre_test_points, post_test_points, is_active, created_at, updated_at
```

### `exam_questions`
```sql
id, exam_part_id, section (enum: pre,post), question_text,
choice_1..4, correct_choice (1-4), order, created_at, updated_at
```

> ⚠️ `correct_choice` ต้อง hidden จาก API serialization ($hidden ใน Model)

### `user_exam_results`
```sql
id, user_id, exam_part_id, section (enum: pre,vdo,post),
score, max_score, percentage, stamp_earned (tinyint),
points_awarded, completed_at, created_at, updated_at
UNIQUE(user_id, exam_part_id, section)
```

### `rewards`
```sql
id, name, description, image_url, points_required,
training_points_required, stock, is_active, created_at, updated_at
```

### `reward_redemptions`
```sql
id, user_id, reward_id, reward_name, points_used,
status (enum: pending,approved,rejected),
shipping_name, shipping_phone, shipping_address,
shipping_province, shipping_district, shipping_subdistrict, shipping_zipcode,
approved_by, approved_at, reject_reason, created_at, updated_at
```

### `banners`
```sql
id, type (enum: main,receipt,exam,reward), image_url, condition_text,
is_active, display_month, display_year, created_at, updated_at
```

### `qa_categories`
```sql
id, name, order, created_at, updated_at
```

### `qa_items`
```sql
id, category_id, question, answer, order, created_at, updated_at
```

---

## PointCalculationService

```
approveReceipt($receipt, $skuData, $totalAmount):
    base_points = floor(totalAmount / 100)  (หรือตาม SKU mapping)
    multiplier  = user.level_multiplier (Platinum=2, Silver=1.5, Gold=1)
    points_awarded = floor(base_points * multiplier)
    → insert points record (type=receipt, reference_id=receipt_id)
    → receipt.points_awarded = points_awarded, status=approved

rejectReceipt($receipt, $reason): status=rejected

adjustPoints($user, $delta, $note): insert points record (type=adjust)

refundRedemptionPoints($redemption): insert points record (type=refund, points=+)
```

---

## Level Multiplier

| ระดับ | Multiplier |
|---|---|
| Platinum | × 2 |
| Silver | × 1.5 |
| Gold | × 1 |

---

## PHP 7.4 Gotchas — ห้ามใช้

| ❌ PHP 8+ | ✅ PHP 7.4 แทน |
|---|---|
| `match()` | `switch/case` |
| `?->` nullsafe | `$x ? $x->y : null` |
| `__construct(private Foo $x)` | ประกาศ property แยก |
| Named arguments `fn(foo: $x)` | positional args เท่านั้น |

---

## VDO Upload (A8 / F5)

- Admin อัพโหลดไฟล์ VDO ได้ที่ฟอร์ม Exam Part
- รองรับ: MP4, WebM, MOV
- ขนาดสูงสุด: **10 MB**
- เก็บใน: `storage/app/public/exams/videos/` (accessible via storage link)
- Column: `exam_parts.vdo_path` (nullable)
- ถ้ามีทั้ง `vdo_url` และ `vdo_path` → LIFF ใช้ `vdo_path` ก่อน
- Migration: `2024_01_01_000009_add_vdo_path_to_exam_parts.php`

---

## Dev Commands

```bash
# รัน server
php artisan serve

# migrate + seed ใหม่
php artisan migrate:fresh --seed

# เพิ่ม column vdo_path (ถ้า DB มีข้อมูลอยู่แล้ว)
php artisan migrate

# storage link (สำหรับ access uploaded files)
php artisan storage:link

# build LIFF assets
npm install && npm run dev
```

---

## Environment Variables (.env)

```env
APP_NAME=CRM-Eucerin
APP_URL=https://your-domain.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_eucerin
DB_USERNAME=...
DB_PASSWORD=...

LINE_CHANNEL_ID=...
LINE_CHANNEL_SECRET=...
LINE_LIFF_ID=...

FILESYSTEM_DISK=public
JWT_SECRET=...   # php artisan jwt:secret
```

---

*ข้อมูล: Activation Hub Co., Ltd. — Confidential*
