<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsKindPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_kind_properties', function (Blueprint $table) {
            $table->id();
            $table->integer("goods_kind_id");
            $table->string("caption")->comment("عنوان فیلد");
            $table->integer("field_type_id")->comment("نوع فیلد: عدد، رشته، ...");
            $table->integer("have_break_line")->default(0)->comment("خط جدید");
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
        Schema::dropIfExists('goods_kind_propertis');
    }
}
