<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductRequestFormIdToTransportPackingForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transport_packing_form', function (Blueprint $table) {
            //
            $table->foreignId("product_request_form_id")->after("transport_id")->nullable();
            $table->foreignId("transport_id")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transport_packing_form', function (Blueprint $table) {
            //
        });
    }
}
