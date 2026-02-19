<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameOfWorkMedicineDoctorInPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {

            $table->integer('right_to_work')->nullable()->comment("حق شغل");
            $table->integer('basis_for_calculation_working_hour')->default(1)->comment("مبنا محاسبه ساعت کاری");
            $table->integer('basis_for_daily_salary')->default(1)->comment("مبنا محاسبه مزد روزانه");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            //
        });
    }
}
