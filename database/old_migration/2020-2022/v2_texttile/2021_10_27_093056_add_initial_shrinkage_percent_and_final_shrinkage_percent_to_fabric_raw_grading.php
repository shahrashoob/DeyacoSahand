<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitialShrinkagePercentAndFinalShrinkagePercentToFabricRawGrading extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fabric_raw_grading', function (Blueprint $table) {
            //
            $table->float("initial_shrinkage_percent")->after("packing_form_item_id")->default(0)->comment("درصد جمع شدگی اولیه");
            $table->float("final_shrinkage_percent")->after("initial_shrinkage_percent")->default(0)->comment("درصد جمع شدگی نهایی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fabric_raw_grading', function (Blueprint $table) {
            //
        });
    }
}
