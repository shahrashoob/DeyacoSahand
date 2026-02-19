<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductionTabInProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // انتقال تب اطلاعات تولید به مسیر محصول
            $table->renameColumn("min_production","min_production_delete");
            $table->renameColumn("max_production","max_production_delete");
            $table->renameColumn("batch","batch_delete");
            $table->renameColumn("extra_production","extra_production_delete");
            $table->renameColumn("percent_of_extra_production","percent_of_extra_production_delete");
            $table->renameColumn("production_channel_id","production_channel_id_delete");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
