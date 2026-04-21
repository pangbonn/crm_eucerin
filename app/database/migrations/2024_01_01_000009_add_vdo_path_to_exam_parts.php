<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVdoPathToExamParts extends Migration
{
    public function up()
    {
        Schema::table('exam_parts', function (Blueprint $table) {
            $table->string('vdo_path')->nullable()->after('vdo_url');
        });
    }

    public function down()
    {
        Schema::table('exam_parts', function (Blueprint $table) {
            $table->dropColumn('vdo_path');
        });
    }
}
