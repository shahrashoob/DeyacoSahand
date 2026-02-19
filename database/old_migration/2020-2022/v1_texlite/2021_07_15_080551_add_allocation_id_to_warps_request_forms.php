<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllocationIdToWarpsRequestForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warps_request_forms', function (Blueprint $table) {
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
        Schema::table('warps_request_forms', function (Blueprint $table) {
            //
        });
    }
}
