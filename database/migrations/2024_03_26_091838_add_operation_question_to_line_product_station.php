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
        Schema::table('line_product_station', function (Blueprint $table) {
            //


            $table->integer("is_need_end_of_operation")->default(0)->
            comment("آیا نیاز به پایان عملیات دارد؟")->
            after("product_caption_in_supplier_system");

            $table->integer("is_need_start_of_operation")->default(0)->
            comment("آیا نیاز به شروع عملیات دارد؟")->
            after("product_caption_in_supplier_system");

            $table->integer("is_need_start_setup")->default(0)->
            comment("آیا نیاز به شروع ستاپ (setup) دارد؟")->
            after("product_caption_in_supplier_system");

            $table->integer("is_ability_to_choose_next_station")->default(0)->
            comment("آیا امکان انتخاب ایستگاه بعدی (در صورت عدم تایید کنترل کیفیت) دارد؟");

            $table->integer("is_need_final_setting")->default(0)->
            comment("آیا نیاز به تنظیمات نهایی دارد؟");

            $table->integer("is_need_allocation_at_first")->default(0)->
            comment("آیا در ابتدا نیاز به تخصیص دارد؟");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
        });
    }
};
