<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeOrderFromProductionCards extends Migration
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
            $table->foreignId("order_id")->nullable()->change();
            $table->foreignId("order_list_id")->nullable()->change();
            $table->foreignId("customer_id")->nullable()->change();
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
