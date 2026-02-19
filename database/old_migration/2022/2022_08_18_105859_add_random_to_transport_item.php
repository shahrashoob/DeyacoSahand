<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRandomToTransportItem extends Migration
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
            $table->string("random")->nullable()->comment("جهت ایجاد لینک کوتاه");
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
