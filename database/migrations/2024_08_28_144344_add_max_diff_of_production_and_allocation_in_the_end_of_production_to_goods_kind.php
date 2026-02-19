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
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
            $table->integer("min_diff_of_production_and_allocation_in_the_end_of_production")->default(0)->
            comment("درصد مجاز کمتر بودن اختلاف کالای تولید شده با مقدار تخصیص کارت تولید/دستور پیمان در زمان پایان تولید");

            $table->integer("max_diff_of_production_and_allocation_in_the_end_of_production")->default(20)->
            comment("درصد مجاز بیشتر بودن اختلاف کالای تولید شده با مقدار تخصیص کارت تولید/دستور پیمان در زمان پایان تولید");

            $table->foreignId("send_sms_in_create_allocation_machine_to_post_id1")->nullable()->
            comment("ارسال پیامک تخصیص کارت تولید به پست های سازمانی");


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kind', function (Blueprint $table) {
            //
        });
    }
};
