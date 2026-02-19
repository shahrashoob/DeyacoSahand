<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExitFormSmsPostIdToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
            $table->foreignId("exit_form_require_permission_post_id")->after("exit_form_require_permission")->
                nullable()->comment("پست مربوطه جهت ارسال پیامک تایید نهایی برگ خروج");
            $table->foreignId("exit_form_require_draft_permission_post_id")->after("exit_form_require_draft_permission")->
                nullable()->comment("پست مربوطه جهت ارسال پیامک تایید پیش نویس برگ خروج");
            $table->foreignId("exit_form_guarding_require_permission_post_id")->after("exit_form_guarding_require_permission")->
            nullable()->comment("پست مربوطه جهت ارسال پیامک تایید نگهبانی برگ خروج");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
