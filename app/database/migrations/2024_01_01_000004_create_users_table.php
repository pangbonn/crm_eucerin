<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('line_uid')->unique();
            $table->string('name');
            $table->string('lastname');
            $table->string('phone', 20);
            $table->date('birthdate');
            $table->string('employee_code')->unique();
            $table->year('start_year');
            $table->boolean('consent_pdpa')->default(false);
            $table->string('photo_url')->nullable();
            $table->enum('level', ['gold', 'silver', 'platinum'])->default('gold');
            $table->boolean('is_active')->default(true);
            $table->timestamp('resigned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('address');
            $table->foreignId('province_id')->constrained();
            $table->foreignId('district_id')->constrained();
            $table->foreignId('subdistrict_id')->constrained();
            $table->string('postal_code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('user_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained();
            $table->string('channel')->nullable(); // DS: shopee/lazada/tiktok / other: free text
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_branches');
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('users');
    }
}
