<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostCooperationTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_cooperation_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id");
            $table->foreignId("cooperation_type_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `post_cooperation_type` comment 'در این جدول لیست انواع همکاری برای پست ذخیره می گردد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_cooperation_type');
    }
}
