<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowShowPostInEmploymentRegisterToPosts extends Migration
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
            $table->integer("allow_show_post_in_employment_register")->default(0)->
            comment("آیا پس از خالی شدن یک جایگاه در گروه شیفت، این پست در لیست پست ها جهت استخدام نمایش داده شود.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employment_register_to_posts', function (Blueprint $table) {
            //
        });
    }
}
