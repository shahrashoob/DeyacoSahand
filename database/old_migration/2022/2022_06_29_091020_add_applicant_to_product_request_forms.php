<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicantToProductRequestForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
            $table->foreignId("applicant_id")->after("code")->nullable()->comment("شناسه درخواست دهنده: ماشین، پیمانکار و ...");
            $table->foreignId("applicant_type_id")->after("code")->nullable()->comment("نوع درخواست دهنده");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_forms', function (Blueprint $table) {
            //
        });
    }
}
