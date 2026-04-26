<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ExpandBannersTypeEnum extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE banners MODIFY COLUMN type ENUM('main','receipt','receipt_cta','exam','exam_cta','reward') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE banners MODIFY COLUMN type ENUM('main','receipt','exam','reward') NOT NULL");
    }
}
