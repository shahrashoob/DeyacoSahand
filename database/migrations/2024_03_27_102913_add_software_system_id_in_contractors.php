<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftwareSystemIdInContractors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contractors', function (Blueprint $table) {

            $table->foreignId('software_system_id')->nullable()->comment("نام سامانه جامع ");
            $table->string('api_url')->nullable()->comment("آدرس سامانه جامع");

            $table->string('api_username')->nullable()->comment("نام کاربری برای API");
            $table->string('api_password')->nullable()->comment(" رمز عبور برای API");
            $table->string('api_key')->nullable()->comment("API Key مربوط به نرم افزار پیمانکار");

            $table->date('start_date_of_contract')->nullable()->comment("تاریخ شروع قرارداد");
            $table->date('end_date_of_contract')->nullable()->comment("تاریخ پایان قرارداد");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contractors', function (Blueprint $table) {
            //
        });
    }
}
