<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePackingTypeIdFromOrderList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_list', function (Blueprint $table) {
            //
            $table->foreignId("packing_type_id")->nullable()->change();
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
        Schema::table('order_list', function (Blueprint $table) {
            //
        });
    }
}
