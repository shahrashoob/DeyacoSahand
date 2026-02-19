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
        Schema::table('customers', function (Blueprint $table) {
            //
            $table->integer("send_order_sms")->comment("آیا پیامک های ثبت سفارش  مشتری ارسال شود؟")->change();
            $table->integer("send_exit_form_sms")->default(0)->comment("آیا پیامک برگ خروج برای مشتری ارسال شود؟");
            $table->integer("send_register_sms")->default(0)->comment("آیا پیامک های ثبت نام برای مشتری ارسال شود؟");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
