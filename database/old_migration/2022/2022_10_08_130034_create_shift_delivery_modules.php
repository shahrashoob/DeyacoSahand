<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftDeliveryModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_delivery_modules', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->integer("presence_in_the_organization_checked")->comment("آیا حضور فرد در سازمان چک شود");
            $table->integer("people_who_have_a_delivery_shift_have_permission_to_exit")->comment("فردی که تحویل شیفت دارد، مجوز خروج دارد؟");
            $table->longText("data");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `shift_delivery_modules` comment 'این جدول برای ذخیره ماژول های تحویل شیفت طراحی شده است، و برای تنظیم باید در بخش تنظیمات/تنظیمات منابع انسانی اقدام کرد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_delivery_modules');
    }
}
