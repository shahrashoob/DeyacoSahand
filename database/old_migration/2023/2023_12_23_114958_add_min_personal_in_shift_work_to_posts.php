<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinPersonalInShiftWorkToPosts extends Migration
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
            $table->integer("min_person_number_in_shift_work")->
            default(1)->
            after("max_person_number_in_shift_work")->
            comment("حداقل تعداد افرادی که باید در یک شیفت کاری در یک پست قرار بگیرند ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_work_to_posts', function (Blueprint $table) {
            //
        });
    }
}
