<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPassedToExamResults extends Migration
{
    public function up()
    {
        Schema::table('user_exam_results', function (Blueprint $table) {
            $table->boolean('passed')->default(false)->after('stamp_earned');
        });
    }

    public function down()
    {
        Schema::table('user_exam_results', function (Blueprint $table) {
            $table->dropColumn('passed');
        });
    }
}
