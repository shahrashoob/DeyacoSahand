<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindClassificationOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_classification_options', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->foreignId("goods_kind_classification_id")->comment("طبقه رسته کالایی");
            $table->timestamps();

        });
        DB::statement("ALTER TABLE `goods_kind_classification_options` comment 'گزینه های هر طبقه تعریف شده در بخش طبقه بندی رسته های  کالایی '");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_classification_option');
    }
}
