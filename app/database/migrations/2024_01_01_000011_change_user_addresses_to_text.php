<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserAddressesToText extends Migration
{
    public function up()
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['subdistrict_id']);
            $table->dropColumn(['province_id', 'district_id', 'subdistrict_id']);
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('province_name', 100)->nullable()->after('address');
            $table->string('district_name', 100)->nullable()->after('province_name');
            $table->string('subdistrict_name', 100)->nullable()->after('district_name');
        });
    }

    public function down()
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['province_name', 'district_name', 'subdistrict_name']);
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->foreignId('province_id')->constrained()->after('address');
            $table->foreignId('district_id')->constrained()->after('province_id');
            $table->foreignId('subdistrict_id')->constrained()->after('district_id');
        });
    }
}
