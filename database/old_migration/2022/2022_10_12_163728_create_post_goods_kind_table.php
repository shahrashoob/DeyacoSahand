<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostGoodsKindTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id");
            $table->foreignId("goods_kind_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `goods_kind_post` comment 'در این جدول دسترسی به رسته های کالایی برای پست مشخص می شود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_post');
    }
}
