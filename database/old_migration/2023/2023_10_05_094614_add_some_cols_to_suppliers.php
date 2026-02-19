<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            //
            $table->foreignId("user_id")->after("id");
            $table->string("code")->after("id");
            $table->string("caption")->after("id")->comment("عنوان تامین کننده");
            $table->string("register_code")->comment("شماره ثبت");
            $table->foreignId("active_status_id")->comment("وضعیت فعال بودن");
            $table->string("ic")->comment("مرکز هزینه");

            $table->integer("get_packing_form_details")->default(1)->comment("آیا جزییات بسته بندی ها از پیمانکار دریافت گردد.");
            $table->integer("input_form_guarding_require_permission")->default(0)->comment("آیا فرم ورود به انبار نیاز به تایید نگهبانی دارد؟");
            $table->integer("input_form_loading_require")->default(0)->comment("آیا فرم ورود نیاز به ارسال (بارگیری) دارد؟");
            $table->integer("input_form_quality_control_permission")->default(0)->comment("آیا فرم های ورود نیاز به تایید کنترل کیفیت دارد؟");


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            //
        });
    }
}
