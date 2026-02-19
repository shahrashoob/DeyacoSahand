<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasSamplingRequiredInProductCreationInGoodKinds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            $table->integer('has_sampling_required_in_product_creation')->default(1)->comment("آیا تولید نمونه آزمایشگاهی برای کالادر طراحی کالا الزامی است؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
        });
    }
}
