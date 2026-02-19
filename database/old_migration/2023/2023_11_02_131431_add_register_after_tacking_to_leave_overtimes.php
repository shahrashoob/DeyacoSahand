<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegisterAfterTackingToLeaveOvertimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_overtimes', function (Blueprint $table) {
            //
            $table->integer("register_after_tacking")->default(0)->
            comment("در صورتی که فرد بعد از انجام مرخضی/ماموریت و ... اقدام به ثبت نمایید، این فیلد T می شود.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_overtimes', function (Blueprint $table) {
            //
        });
    }
}
