<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExitFormLeadingRequirePermissionToContractor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->integer( "exit_form_loading_require_permission" )->default( 0 )->
            after( "exit_form_guarding_require_permission_post_id" )->
            comment( "آیا برگ خروج نیاز به ارسال (بارگیری) دارد؟" );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contractor', function (Blueprint $table) {
            //
        });
    }
}
