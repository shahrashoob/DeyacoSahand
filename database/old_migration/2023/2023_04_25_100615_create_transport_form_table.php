<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId("transport_id");
            $table->foreignId("form_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `transport_form` comment 'به ازای هر بار، ممکن است یک یا چند برگ خروج خارج شوند'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_form');
    }
}
