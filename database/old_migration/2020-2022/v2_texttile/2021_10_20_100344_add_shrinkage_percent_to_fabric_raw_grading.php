<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShrinkagePercentToFabricRawGrading extends Migration
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
            $table->float( "second_shrinkage_percent" )->nullable()->comment( "درصد جمع شدگی ثانویه" );

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
