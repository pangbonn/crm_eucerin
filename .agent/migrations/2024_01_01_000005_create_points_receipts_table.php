<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsReceiptsTable extends Migration
{
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('images'); // array of file paths, max 5
            $table->decimal('sales_amount', 12, 2)->nullable();
            $table->json('sku_data')->nullable(); // [{sku, qty, base_points}]
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('admins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->integer('points_awarded')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points'); // positive = add, negative = deduct
            $table->enum('source', ['receipt', 'exam_pre', 'exam_post', 'vdo_stamp', 'manual', 'redemption']);
            $table->unsignedBigInteger('reference_id')->nullable(); // receipt_id / exam_result_id
            $table->string('reference_type')->nullable(); // morph type
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('points');
        Schema::dropIfExists('receipts');
    }
}
