<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllocationIdToFabricRawDesignForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fabric_raw_design_forms', function (Blueprint $table) {
            //
            $table->foreignId("allocation_id")->comment(" شمارنده تخصیص ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fabric_raw_design_forms', function (Blueprint $table) {
            //
        });
    }
}
