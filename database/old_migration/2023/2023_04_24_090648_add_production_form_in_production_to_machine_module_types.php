<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionFormInProductionToMachineModuleTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_module_types', function (Blueprint $table) {
            //
            $table->foreignId("production_form_in_production")->nullable()->comment("وضعیت در حال تولید فرم تولید ماژول های ماشین");
            $table->foreignId("production_form_terminated")->nullable()->comment(" وضعیت خاتمه یافته فرم تولید ماژول های ماشین");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_module_types', function (Blueprint $table) {
            //
        });
    }
}
