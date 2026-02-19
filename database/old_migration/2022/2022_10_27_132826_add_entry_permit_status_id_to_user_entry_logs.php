<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntryPermitStatusIdToUserEntryLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_entry_logs', function (Blueprint $table) {
            //
            $table->foreignId("entry_permit_status_id")->default(461000200)->after("entry_register_user_id")->comment("وضعیت مجوز ورود در زمان ثبت");
            $table->foreignId("exit_permit_status_id")->default(461000200)->after("exit_register_user_id")->comment("وضعیت مجوز خروج در زمان ثبت");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_entry_logs', function (Blueprint $table) {
            //
        });
    }
}
