<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialSoftwareTransKindTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_software_trans_kind_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->string("caption_en");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `financial_software_trans_kind_types` comment 'در این جدول انواع تراکنش هایی که می توان در نرم افزار مالی ثبت نمود ذخیره می گردد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_software_trans_kind_types');
    }
}
