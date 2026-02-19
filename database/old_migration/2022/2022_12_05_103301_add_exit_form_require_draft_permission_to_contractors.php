<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExitFormRequireDraftPermissionToContractors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contractors', function (Blueprint $table) {
            //
            $table->integer("exit_form_require_draft_permission")->after("exit_form_require_permission")->
            default(1)->
            comment("آیا پیش نویس برگ خروج (فرم خروج از انبار) نیاز به مجوز دارد؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contractors', function (Blueprint $table) {
            //
        });
    }
}
