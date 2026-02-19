<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_settings', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("caption");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `goods_kind_settings` comment 'جدول تنظیمات پیش فرض تعریف کالا به ازای هر رسته کالایی'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_settings');
    }
}
