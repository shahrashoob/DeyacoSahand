<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUseOfSmartObjectToWarehouseHandling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_handling', function (Blueprint $table) {
            //
            $table->integer("use_of_smart_object")->default(0)->comment("نوع انبارش: دستی/ یا استفاده از اشیاء هوشمند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_handling', function (Blueprint $table) {
            //
        });
    }
}
