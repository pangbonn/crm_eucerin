<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamTables extends Migration
{
    public function up()
    {
        Schema::create('exam_parts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('part_number');
            $table->string('banner_image')->nullable();
            $table->string('vdo_url')->nullable();
            $table->integer('pre_test_points')->default(10);
            $table->integer('post_test_points')->default(8);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_part_id')->constrained()->cascadeOnDelete();
            $table->enum('section', ['pre', 'post']);
            $table->text('question_text');
            $table->string('choice_1');
            $table->string('choice_2');
            $table->string('choice_3');
            $table->string('choice_4');
            $table->tinyInteger('correct_choice'); // 1-4
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('user_exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_part_id')->constrained()->cascadeOnDelete();
            $table->enum('section', ['pre', 'vdo', 'post']);
            $table->integer('score')->default(0);
            $table->integer('max_score')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('stamp_earned')->default(false); // only for vdo section
            $table->integer('points_awarded')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // ทำแล้วไม่ให้ทำซ้ำ
            $table->unique(['user_id', 'exam_part_id', 'section']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_exam_results');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exam_parts');
    }
}
