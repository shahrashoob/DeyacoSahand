<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->integer("sent_address_place_type_of_transport")->default(1)->comment("نوع آدرس محل ارسال بار توسط پیمانکار1-محل کارخانه0-محل مشتری");
            $table->integer("duration_of_default_of_product")->default(1)->comment("مدت زمان پیش فرض  تحویل کالا توسط پیمانکار(روز)");
            $table->integer("is_order_registration_date_chosen_by_contractor")->default(0)->comment("ایا تاریخ ثبت سفارش توسط پیمانکار انتخاب شود1-بله0-خیر");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            //
        });
    }
};
