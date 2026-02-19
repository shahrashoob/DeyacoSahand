<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePackintTypeIdInProductionCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_cards', function (Blueprint $table) {
            //
            $table->renameColumn("packing_type_id","packing_type_id_remove");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_cards', function (Blueprint $table) {
            //
        });
    }
}
