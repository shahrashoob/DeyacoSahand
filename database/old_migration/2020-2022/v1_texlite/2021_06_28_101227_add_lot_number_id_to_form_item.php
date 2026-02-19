<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotNumberIdToFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_item', function (Blueprint $table) {
            //
            $table->foreignId("lot_number_id")->nullable()->comment("شماره همبافت (شید | لات) ");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_item', function (Blueprint $table) {
            //
        });
    }
}
