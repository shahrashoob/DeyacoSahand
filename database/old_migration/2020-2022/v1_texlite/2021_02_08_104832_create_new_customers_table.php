<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_customers', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("caption")->default("");
            $table->integer("channel_id")->default(-100)->comment("کانال توزیع");
            $table->string("sub_channel_caption")->nullable()->comment("کانال توزیع - دسته دوم");
            $table->string("error")->nullable();


            $table->integer("user_id");
            $table->integer("gender_id");
            $table->date("birth_date")->nullable();
            $table->integer("customer_type_id");

            $table->integer("tariff_id")->default(0)->comment("نوع تعرفه");
            $table->integer("cash_off_percent")->default(0)->comment("درصد تخقیق نقدی");

            $table->integer("bail_amount")->default(0)->comment("میزان وثیقه");
            $table->string("economic_number")->nullable()->comment("شماره اقتصادی");
            $table->string("detailed_code")->nullable()->comment("نوسا کد تفظیلی ");
            $table->string("center_detailed_code")->nullable()->comment("کد مرکز تفظیلی ");
            $table->integer("priority_id")->default(3)->comment("اولویت سفارش های مشتری");
            $table->string("register_code")->nullable()->comment("شماره ثبت / شماره ملی");
            $table->integer("province_id")->default(9999)->comment("استان مشتری");

            $table->string("national_code")->nullable();
            $table->string("national_id")->nullable();

            $table->integer("order_permission_type_1")->default(0);
            $table->integer("order_permission_type_2")->default(0);
            $table->integer("order_permission_type_3")->default(0);
            $table->integer("order_permission_type_4")->default(0);
            $table->integer("order_permission_type_5")->default(0);
            $table->integer("order_permission_type_6")->default(0);
            $table->integer("order_permission_type_7")->default(0);
            $table->integer("order_permission_type_8")->default(0);
            $table->integer("order_permission_type_9")->default(0);
            $table->integer("order_permission_type_10")->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_customers');
    }
}
