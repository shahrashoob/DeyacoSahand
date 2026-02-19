<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->integer("product_id");
            $table->integer("min_buy");
            $table->integer("max_buy");
            $table->dateTime("start_datetime");
            $table->dateTime("end_datetime");
            $table->integer("channel_type_id");
            $table->integer("customer_id")->nullable();
            $table->float("percent_off")->default(0)->nullable();
            $table->float("percent_free")->default(0)->nullable();
            $table->integer("offer_type_id")->comment("");
            $table->integer("status_id")->default(522000100)->comment("status_type_id=5220");
            $table->timestamps();
        });
    }

    /** $2y$10$Sk1FbS6Q0y0sulm1HS.tsOuZQ9ust/BLyLgdRVGUGSv4a98cIM3rq
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
