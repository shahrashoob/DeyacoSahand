<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmationIsRequiredToSelectionSelectors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('selection_selectors', function (Blueprint $table) {
            $table->integer("confirmation_is_required")->default(0)->comment("ایا رد و تایید گزینش برای گزینش کننده الزامی است؟");
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
