<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId("product_id")->constrained()->nullabe();
            $table->foreignId("line_id")->constrained()->nullabe();

            $table->float("min_of_production")->default(0)->comment("per hour");
            $table->float("max_of_production")->default(0)->comment("per hour");
            $table->float("efficiency")->default(2)->comment("efficiency for sub_productivity_index ");
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
        Schema::dropIfExists('line_product');
    }
}
