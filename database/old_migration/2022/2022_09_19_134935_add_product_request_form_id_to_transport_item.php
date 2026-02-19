<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductRequestFormIdToTransportItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transport_item', function (Blueprint $table) {
            //
            $table->foreignId("product_request_form_id")->nullable()->after("transport_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transport_item', function (Blueprint $table) {
            //
        });
    }
}
