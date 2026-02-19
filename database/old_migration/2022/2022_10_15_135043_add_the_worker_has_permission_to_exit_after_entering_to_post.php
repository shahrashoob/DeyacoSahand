<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTheWorkerHasPermissionToExitAfterEnteringToPost extends Migration
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
            $table->integer("the_worker_has_permission_to_leave_after_entering")->default(1)->
            comment("افزاد شاغل در پست پس از ورود مجوز خروج دارند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exit_after_entering_to_post', function (Blueprint $table) {
            //
        });
    }
}
