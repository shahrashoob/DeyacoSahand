<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveOvertimeGroupIdToLeaveOvertimeTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_overtime_types', function (Blueprint $table) {
            //
            $table->foreignId("leave_overtime_group_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_overtime_types', function (Blueprint $table) {
            //
        });
    }
}
