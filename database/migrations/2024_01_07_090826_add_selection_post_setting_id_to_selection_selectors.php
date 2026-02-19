<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelectionPostSettingIdToSelectionSelectors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('selection_selectors', function (Blueprint $table) {
            //
            $table->foreignId("selection_post_setting_id")->comment("شناسه گزینش در پست های سازمانی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('selection_selectors', function (Blueprint $table) {
            //
        });
    }
}
