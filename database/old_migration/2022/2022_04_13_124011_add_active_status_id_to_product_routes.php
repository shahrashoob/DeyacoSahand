<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveStatusIdToProductRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_routes', function (Blueprint $table) {
            //
            $table->foreignId("active_status_id")->default(1200)->comment("فعال/غیرفعال");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_routes', function (Blueprint $table) {
            //
        });
    }
}
