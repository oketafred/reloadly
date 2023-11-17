<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeZoneDetailsToTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topups', function (Blueprint $table) {
            $table->unsignedBigInteger('timezone_id')->nullable()->default(null)->after('status');
            $table->foreign('timezone_id')->references('id')->on('timezones');
            $table->dateTimeTz('scheduled_datetime')->nullable()->default(null)->after('timezone_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topups', function (Blueprint $table) {
            $table->dropColumn(['timezone_id', 'scheduled_datetime']);
        });
    }
}
