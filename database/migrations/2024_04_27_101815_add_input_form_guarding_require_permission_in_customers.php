<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputFormGuardingRequirePermissionInCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('input_form_guarding_require_permission')->after('the_max_day_allowed_to_conform_exit_form_to')->default(0)->comment('ایا فرم ورود به انبار نیاز به تایید نگهبانی دارد؟');
            $table->integer('input_form_loading_require')->after('input_form_guarding_require_permission')->default(0)->comment(' آیا فرم ورود نیاز به ارسال (بارگیری) دارد؟');
            $table->integer('input_form_quality_control_permission')->after('input_form_loading_require')->default(0)->comment('آیا فرم های ورود نیاز به تایید کنترل کیفیت دارد');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
