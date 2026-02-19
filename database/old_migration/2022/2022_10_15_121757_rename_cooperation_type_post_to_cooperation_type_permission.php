<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCooperationTypePostToCooperationTypePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cooperation_type_post', function (Blueprint $table) {
            //
            $table->rename("cooperation_type_permission");
        });
        DB::statement("ALTER TABLE `cooperation_type_permission` comment 'دسترسی به نوع همکاری ها'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cooperation_type_permission', function (Blueprint $table) {
            //
        });
    }
}
