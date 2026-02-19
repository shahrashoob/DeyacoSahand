<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalAmountToFabricRawGrading extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fabric_raw_grading', function (Blueprint $table) {
            $table->float( "final_amount" )->nullable()->
            after( "amount" )->comment( "مقدار نهایی در هنگام بسته بندی" );

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
