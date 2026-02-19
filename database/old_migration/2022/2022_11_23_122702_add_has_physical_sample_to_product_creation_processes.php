<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasPhysicalSampleToProductCreationProcesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
            $table->integer("has_physical_sample")->comment("آیا کالای جدید دارای نمونه فیزیکی می باشد؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
        });
    }
}
