<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEmploymentNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employment_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->nullable()->comment('وضعیت');
            $table->foreignId('post_id')->nullable()->comment('وضعیت');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE employment_notification_settings COMMENT "جدول مرتبط با پیامک های همکاری با ما"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employment_notification_settings');
    }
}
