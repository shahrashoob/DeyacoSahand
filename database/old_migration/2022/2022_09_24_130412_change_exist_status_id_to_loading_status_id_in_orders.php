<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeExistStatusIdToLoadingStatusIdInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->integer("exit_status_id")->comment("ایا سفارش مجوز بارگیری دارد؟")->change();
            $table->renameColumn("exit_status_id","loading_status_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loading_status_id_in_orders', function (Blueprint $table) {
            //
        });
    }
}
