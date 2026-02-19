<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferenceCaptionToApplicantTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applicant_types', function (Blueprint $table) {
            //
            $table->string("reference_caption")->
            comment("عنوان شماره مرجع: در فرم درخواست کالا از انبار با توجه به نوع درخواست یک شماره مرجع نمایش داده می شود.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applicant_types', function (Blueprint $table) {
            //
        });
    }
}
