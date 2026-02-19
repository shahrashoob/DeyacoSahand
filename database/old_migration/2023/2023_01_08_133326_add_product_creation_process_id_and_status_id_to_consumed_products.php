<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductCreationProcessIdAndStatusIdToConsumedProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consumed_products', function (Blueprint $table) {
            //
            $table->foreignId("product_creation_process_id")->comment("فرم طراحی که منجر به طراحی این کالا شده است.");
            $table->foreignId("status_id")->default("3400001")->comment("وضعیت پیش فرض: کالا طراحی شده");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consumed_products', function (Blueprint $table) {
            //
        });
    }
}
