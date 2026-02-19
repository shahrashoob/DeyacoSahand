<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePupUpPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pup_up_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId("pup_up_id");
            $table->foreignId("post_id");
            $table->foreignId("user_id");
            $table->integer("number")->default(0)->comment("تعداد نمایش");
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
        Schema::dropIfExists('pup_up_post');
    }
}
