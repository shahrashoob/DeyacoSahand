<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRawMaterialRequestSamplingAlgorithmTypeIdToMachine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machines', function (Blueprint $table) {
            //
            $table->foreignId("raw_material_request_sampling_algorithm_type_id")->nullable()->
            comment(
                "الگوریتم درخواست مواد اولیه برای کارت های نمونه گیری"
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine', function (Blueprint $table) {
            //
        });
    }
}
