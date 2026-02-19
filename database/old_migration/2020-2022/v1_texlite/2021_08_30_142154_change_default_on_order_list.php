<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDefaultOnOrderList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_list', function (Blueprint $table) {
            $table->integer("from_order_id")->default(null)->nullable()->change();
            $table->integer("from_order_list_id")->default(null)->nullable()->change();
            $table->integer("from_production_card_id")->default(null)->nullable()->change();

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
