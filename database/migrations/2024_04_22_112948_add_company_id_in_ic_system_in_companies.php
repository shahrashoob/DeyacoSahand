<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdInIcSystemInCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('company_id_in_ic_system')->nullable()->comment('ایدی شرکت در ای سی');
            $table->foreignId('user_id')->nullable();
            $table->string('register_code')->nullable()->comment('شماره ثبت');
            $table->string('national_code')->nullable()->comment('شناسه ملی');
            $table->foreignId('holding_id')->nullable()->change();
            $table->string('caption')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
}
