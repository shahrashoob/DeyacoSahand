<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTheMaxDayAllowedToConformExitFormToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
            $table->integer("the_max_day_allowed_to_conform_exit_form_to")->default(1)->comment("حداکثر زمان (روز) مجاز تایید برگ خروج از انبار");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conform_exit_form_to_customers', function (Blueprint $table) {
            //
        });
    }
}
