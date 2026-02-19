<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRequiredFromGoodsKindProperty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kind_properties', function (Blueprint $table) {
            //
            $table->dropColumn("required");
            // به دلیل انتقال اجباری بودن به گروه کالایی
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kind_property', function (Blueprint $table) {
            //
        });
    }
}
