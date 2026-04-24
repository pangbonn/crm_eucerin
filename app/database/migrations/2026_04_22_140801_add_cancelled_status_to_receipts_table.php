<?php

use Illuminate\Database\Migrations\Migration;

class AddCancelledStatusToReceiptsTable extends Migration
{
    public function up()
    {
        \DB::statement("ALTER TABLE receipts MODIFY COLUMN status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        \DB::statement("ALTER TABLE receipts MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
}
