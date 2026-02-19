<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeMachineLogIdToProductionForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
            $table->foreignId("loading_machine_log_id")->nullable()->comment("قطب شروع در انتظار بارگذاری");
            $table->foreignId("in_the_weaving_machine_log_id")->nullable()->comment("قطب شروع در حال بافت ");
            $table->foreignId("in_the_finishing_weaving_machine_log_id")->nullable()->comment("قطب شروع در حال بافت پارچه پایانی");
            $table->foreignId("extraction_machine_log_id")->nullable()->comment("قطب استخراج پارچه");

            $table->foreignId("start_machine_log_id")->comment("قطب شروع بافت پارچه")->change();
            $table->foreignId("end_of_machine_log_id")->comment("قطب پایان بافت پارچه")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
        });
    }
}
