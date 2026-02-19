<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_classifications', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->foreignId("goods_kind_id");
            $table->foreignId("goods_kind_classification_type_id")->comment("نوع طبقه بندی");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `goods_kind_classifications` comment ' طبقه بندی رسته های کالایی  '");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_kind_classification');
    }
}
