<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingMetaToRewardRedemptionsTable extends Migration
{
    public function up()
    {
        Schema::table('reward_redemptions', function (Blueprint $table) {
            $table->boolean('use_registered_address')->default(true)->after('status');
            $table->string('shipping_carrier', 100)->nullable()->after('shipping_postal_code');
            $table->string('tracking_number', 100)->nullable()->after('shipping_carrier');
        });
    }

    public function down()
    {
        Schema::table('reward_redemptions', function (Blueprint $table) {
            $table->dropColumn([
                'use_registered_address',
                'shipping_carrier',
                'tracking_number',
            ]);
        });
    }
}
