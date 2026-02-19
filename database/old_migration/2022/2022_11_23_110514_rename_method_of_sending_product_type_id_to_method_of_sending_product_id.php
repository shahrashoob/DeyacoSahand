<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameMethodOfSendingProductTypeIdToMethodOfSendingProductId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
            $table->renameColumn("method_of_sending_product_type_id","method_of_sending_product_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('method_of_sending_product_id', function (Blueprint $table) {
            //
        });
    }
}
