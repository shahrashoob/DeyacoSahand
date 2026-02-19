<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForMissionNumberOfRegisterAfterTackingToPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
            $table->integer("for_mission_number_of_register_after_tacking_to_posts")->
            after("for_mission_a_few_top_levels_must_confirm_time_level")->
            default(3)->
            comment("تعداد مواردی که فرد می تواند پس از انجام ماموریت، ماموریت خود را در سامانه ثبت نماید، n بار در سال است.");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
        });
    }
}
