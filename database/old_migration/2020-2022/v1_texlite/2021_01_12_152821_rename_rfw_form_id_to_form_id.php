<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRfwFormIdToFormId extends Migration
{
    /**
     * Run the migrations.
     * composer require doctrine/dbal
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('form_product', function (Blueprint $table) {
//            $table->renameColumn("rfw_form_id","form_id");
//        });
        Schema::table('warehouse_product', function (Blueprint $table) {
            $table->renameColumn("rfw_form_id","form_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_product', function (Blueprint $table) {
            $table->renameColumn("form_id","rfw_form_id");
        });
        Schema::table('warehouse_product', function (Blueprint $table) {
            $table->renameColumn("form_id","rfw_form_id");
        });
    }
}
