<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxNumberOfSequencesToProductionChannelTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_channel_types', function (Blueprint $table) {
            //
            $table->integer("max_number_of_sequences")->default(1)->
            comment("تعداد کانال هایی که از یک نوع کانال می توانند پست سر هم برای ماشین ایجاد شوند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_channel_types', function (Blueprint $table) {
            //
        });
    }
}
