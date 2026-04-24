<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuestionsPerSessionToExamParts extends Migration
{
    public function up()
    {
        Schema::table('exam_parts', function (Blueprint $table) {
            $table->unsignedInteger('questions_per_session')->default(10)->after('post_test_points');
        });
    }

    public function down()
    {
        Schema::table('exam_parts', function (Blueprint $table) {
            $table->dropColumn('questions_per_session');
        });
    }
}
