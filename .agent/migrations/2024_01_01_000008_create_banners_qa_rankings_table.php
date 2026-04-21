<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersQaRankingsTable extends Migration
{
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['main', 'receipt', 'exam', 'reward']);
            $table->string('image_url');
            $table->string('link_url')->nullable();
            $table->text('condition_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('display_month')->nullable(); // 1-12
            $table->year('display_year')->nullable();
            $table->timestamps();
        });

        Schema::create('qa_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('qa_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('qa_categories')->cascadeOnDelete();
            $table->text('question');
            $table->text('answer');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('total_points')->default(0);
            $table->integer('rank')->nullable();
            $table->tinyInteger('period_month'); // 1-12
            $table->year('period_year');
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['user_id', 'period_month', 'period_year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rankings');
        Schema::dropIfExists('qa_items');
        Schema::dropIfExists('qa_categories');
        Schema::dropIfExists('banners');
    }
}
