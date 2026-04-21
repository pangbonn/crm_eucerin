<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->enum('zone', [
                'BKK1','BKK2','BKK3','BKK4','BKK6','BKK7','BKK8','BKK9',
                'NU','NL','NEU','NEM','NEL','MID','EAST','SOUTH','DS','OTHER'
            ]);
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->string('shop_type'); // Channel
            $table->string('shop_name');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
