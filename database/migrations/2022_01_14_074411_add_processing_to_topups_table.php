<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddProcessingToTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE topups CHANGE COLUMN status status ENUM('PENDING','SUCCESS','FAIL','PENDING_PAYMENT', 'REFUNDED', 'PROCESSING') NOT NULL DEFAULT 'PENDING_PAYMENT'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE topups CHANGE COLUMN status status ENUM('PENDING','SUCCESS','FAIL','PENDING_PAYMENT', 'REFUNDED') NOT NULL DEFAULT 'PENDING_PAYMENT'");
    }
}
