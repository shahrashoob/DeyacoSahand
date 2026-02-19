<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTheWorkerHasPermissionToEnteringFromStaticIpToPosts extends Migration
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
            $table->integer("the_worker_has_permission_to_entering_from_static_ip")->default(1)->comment("آیا شاغلین مشغول در پست از طریق ای پی ثابت می توانند به سامانه وارد شوند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entering_from_static_ip_to_posts', function (Blueprint $table) {
            //
        });
    }
}
