<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToLinePost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_post', function (Blueprint $table) {
            //

            $table->foreignId("line_id")->nullable()->change();
            $table->foreignId("station_id")->nullable();
            $table->foreignId("machine_type_id")->nullable();
            $table->foreignId("machine_id")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('line_post', function (Blueprint $table) {
            //
        });
    }
}
