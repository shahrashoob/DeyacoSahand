<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSplitShiftTypeGroupIdToSplitShiftTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('split_shift_types', function (Blueprint $table) {
            //
            $table->integer("split_shift_type_group_id")->comment("صبح کار (1)، ظهر کار (2)، شب کار (3)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('split_shift_types', function (Blueprint $table) {
            //
        });
    }
}
