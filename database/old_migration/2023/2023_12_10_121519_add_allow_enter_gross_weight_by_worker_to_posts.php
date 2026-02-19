<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowEnterGrossWeightByWorkerToPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
            $table->integer("allow_enter_gross_weight_by_worker_to_posts")->default(0)->comment("در صورتی که هیچ باسکولی برای ثبت وزن ناخالص برای پست ها تعریف نشده بود، آیا مقدار را از اپراتور دریافت کند");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
        });
    }
}
