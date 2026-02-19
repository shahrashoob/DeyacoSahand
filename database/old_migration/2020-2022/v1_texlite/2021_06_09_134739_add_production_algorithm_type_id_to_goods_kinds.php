<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionAlgorithmTypeIdToGoodsKinds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            $table->foreignId("production_algorithm_type_id")->constrained()
                  ->comment("روش(الگوريتم) برنامه ريزي توليد");
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
            $table->foreignId("production_algorithm_type_id")->constrained()
                  ->comment("روش(الگوريتم) برنامه ريزي توليد");
        });
    }
}
