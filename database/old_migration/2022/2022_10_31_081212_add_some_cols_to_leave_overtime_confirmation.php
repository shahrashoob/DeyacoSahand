<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToLeaveOvertimeConfirmation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_overtime_confirmation', function (Blueprint $table) {
            //
            $table->integer("number_top_levels_must_confirm")->comment("تعداد سطح بالا که باید مرخصی را تایید کنند.")->default(1);
            $table->integer("number_top_levels_confirmed")->comment("تعدا سطخ بالا که مرخصی را تایید کردند.")->default(0);

            $table->dropColumn("current_confirm_user_id");
            $table->dropColumn("replace_post_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_overtime_confirmation', function (Blueprint $table) {
            //
        });
    }
}
