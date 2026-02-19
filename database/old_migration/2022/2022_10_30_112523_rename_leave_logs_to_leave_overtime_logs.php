<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLeaveLogsToLeaveOvertimeLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_logs', function (Blueprint $table) {
            //
            $table->renameColumn("leave_id","leave_overtime_id");
            $table->rename( "leave_overtime_logs" );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_overtime_logs', function (Blueprint $table) {
            //
        });
    }
}
