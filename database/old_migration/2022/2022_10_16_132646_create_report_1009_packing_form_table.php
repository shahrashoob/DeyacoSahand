<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReport1009PackingFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_1009_packing_form', function (Blueprint $table) {

            $table->foreignId("owner_user_id");
            $table->foreignId("packing_form_id");
            $table->foreignId("packing_form_item_id");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_1009_packing_form');
    }
}
