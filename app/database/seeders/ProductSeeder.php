<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Eucerin Spotless Brightening Booster Serum',
                'description' => 'เซรั่มบูสเตอร์ลดเลือนจุดด่างดำ ช่วยให้ผิวดูกระจ่างใสขึ้น เหมาะสำหรับใช้เป็นประจำทุกวัน',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Eucerin Hyaluron-Filler Day SPF30',
                'description' => 'เดย์ครีมเติมความชุ่มชื้น พร้อมปกป้องผิวจากแสงแดด ลดเลือนริ้วรอยแรกเริ่ม',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Eucerin Pro Acne Solution A.I. Clearing Treatment',
                'description' => 'ผลิตภัณฑ์ดูแลผิวเป็นสิวง่าย ช่วยลดการอุดตันและรอยสิว เนื้อบางเบา ซึมไว',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Eucerin pH5 Shower Oil',
                'description' => 'ออยล์อาบน้ำสำหรับผิวบอบบาง ช่วยคงสมดุลผิว ลดความแห้งตึงหลังอาบน้ำ',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Eucerin Sun Dry Touch Acne Oil Control SPF50+',
                'description' => 'กันแดดสำหรับผิวมันและเป็นสิวง่าย คุมมันยาวนาน พร้อมปกป้อง UVA/UVB',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Eucerin UltraSENSITIVE Soothing Care',
                'description' => 'มอยส์เจอไรเซอร์สำหรับผิวแพ้ง่ายมาก ช่วยปลอบประโลมผิว ลดการระคายเคือง',
                'image' => null,
                'is_active' => false,
            ],
            [
                'name' => 'Eucerin UreaRepair PLUS 5% Urea Lotion',
                'description' => 'โลชั่นบำรุงผิวแห้งมาก เติมความชุ่มชื้นยาวนาน ลดอาการผิวแห้งลอก',
                'image' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Eucerin DermatoCLEAN Micellar Foam',
                'description' => 'โฟมล้างหน้าสำหรับผิวบอบบาง ช่วยทำความสะอาดและคงความชุ่มชื้นของผิว',
                'image' => null,
                'is_active' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
