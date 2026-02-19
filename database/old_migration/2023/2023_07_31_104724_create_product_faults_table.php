<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_faults', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("string");
            $table->integer("need_to_move_shift")->comment("نیاز به جابجایی شیفت دارد");
            $table->integer("need_to_confirmation")->comment("نیاز به تایید دارد");
            $table->string("sms_to_posts")->comment("لیست پست هایی که باید پیامک زده شود.");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `product_faults` comment 'جدول همه عیب هایی که ممکن است در کالا اتفاق بیفتد'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_faults');
    }
}
