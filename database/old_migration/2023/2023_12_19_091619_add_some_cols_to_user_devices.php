<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToUserDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            //
            $table->integer('device_type_id')->comment("نوع دستگاه");;
            $table->string('platform')->comment("پلت فورم دستگاه");;;
            $table->string('platform_version')->comment("ورژن پلت فورم دستگاه");;;
            $table->string('browser')->comment("نوع مرورگر دستگاه");;;
            $table->string('browser_version')->comment("ورژن مررگز دستگاه");;;
            $table->string('device_brand')->comment("برند دستگاه");;;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            //
        });
    }
}
