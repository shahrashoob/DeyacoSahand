<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePupUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pup_ups', function (Blueprint $table) {
            $table->id();
            $table->string("caption")->nullable();
            $table->text("message")->nullable();
            $table->string("version")->comment("وروژن نرم افزار")->default("");
            $table->integer("number_of_show")->comment("تعداد نمایش")->default(1);
            $table->dateTime("start_date")->comment("تاریخ شروع");
            $table->dateTime("end_of_date")->comment("تاریخ پایان");
            $table->string("post_show_ids")->default("")->comment("لیست پست هایی که باید برای آنها نمایش داده شود.");
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
        Schema::dropIfExists('pup_ups');
    }
}
