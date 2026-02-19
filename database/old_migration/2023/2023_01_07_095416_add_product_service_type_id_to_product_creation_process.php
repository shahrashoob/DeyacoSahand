<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductServiceTypeIdToProductCreationProcess extends Migration
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
            $table->foreignId("product_service_type_id")->comment("نوع کالا/خدمت");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
        });
    }
}
