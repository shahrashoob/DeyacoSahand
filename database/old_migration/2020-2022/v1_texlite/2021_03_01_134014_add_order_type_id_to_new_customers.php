<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderTypeIdToNewCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_customers', function (Blueprint $table) {
            //
            $table->integer("order_type_id")->default(0)->comment("نوع فروش به مشتری");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_customers', function (Blueprint $table) {
            //
        });
    }
}
