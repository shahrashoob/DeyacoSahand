<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindClassificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_classification_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `goods_kind_classification_types` comment 'نوع طبقه بندی در طبقه بندی رسته های کالایی ( اصلی، فرعی) '");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_classification_types');
    }
}
