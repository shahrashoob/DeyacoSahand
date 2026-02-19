<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id");
            $table->foreignId("contractor_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `contractor_post` comment 'در این جدول دسترسی به پیمانکاران مشخص می گردد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_post');
    }
}
