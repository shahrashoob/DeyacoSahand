<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityNumberToMenuTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_types', function (Blueprint $table) {
            //
            $table->integer("priority_number")->default(1)->comment("اولوییت نمایش منو ها");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_types', function (Blueprint $table) {
            //
        });
    }
}
