<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLastRunDateTimeInPostEvaluations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_evaluations', function (Blueprint $table) {
            $table->dateTime("last_run_date_time")->nullable()->change();
            $table->dateTime("next_must_run_date_time")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_evaluations', function (Blueprint $table) {
            //
        });
    }
}
