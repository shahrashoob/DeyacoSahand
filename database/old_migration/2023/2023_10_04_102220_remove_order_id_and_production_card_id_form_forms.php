<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveOrderIdAndProductionCardIdFormForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('forms', function (Blueprint $table) {
           $table->renameColumn("production_card_id","production_card_id_remove");
           $table->renameColumn("order_id","order_id_remove");
           $table->renameColumn("order_list_id","order_list_id_remove");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
