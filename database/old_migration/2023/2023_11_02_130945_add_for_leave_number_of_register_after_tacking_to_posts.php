<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForLeaveNumberOfRegisterAfterTackingToPosts extends Migration
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
            $table->integer("for_leave_number_of_register_after_tacking")->default(3)->after("for_leave_required_to_replace_person")->
            comment("تعداد مواردی که فرد می تواند پس از انجام مرخصی، مرخصی خود را در سامانه ثبت نماید، n بار در سال است.");
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
