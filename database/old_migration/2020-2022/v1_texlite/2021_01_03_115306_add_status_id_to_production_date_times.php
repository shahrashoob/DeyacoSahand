<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIdToProductionDatetimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_date_times', function (Blueprint $table) {
            //
            $table->integer("status_id")->default(510000100)->comment("");// تایید نشده
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_daatetime', function (Blueprint $table) {
            //
        });
    }
}
