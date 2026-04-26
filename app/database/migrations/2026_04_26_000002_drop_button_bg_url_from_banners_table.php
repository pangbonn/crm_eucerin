<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropButtonBgUrlFromBannersTable extends Migration
{
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('button_bg_url');
        });
    }

    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('button_bg_url')->nullable()->after('image_url');
        });
    }
}
