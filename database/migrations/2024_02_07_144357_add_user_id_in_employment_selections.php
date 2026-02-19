<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdInEmploymentSelections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employment_selection', function (Blueprint $table) {
            $table->foreignId('user_id')->after("selection_id")->nullable()->comment("ایدی کاربر گزینش کننده");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employment_selection', function (Blueprint $table) {
            //
        });
    }
}
