<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShrinkagePercentToProductionFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_form_item', function (Blueprint $table) {
            $table->float("shrinkage_percent")->nullable()->comment("در صد جمع شدگی 0-100");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_form_item', function (Blueprint $table) {
            //
        });
    }
}
