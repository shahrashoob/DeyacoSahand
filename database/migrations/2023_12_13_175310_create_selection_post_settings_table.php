<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectionPostSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_post_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->comment("پست");
            $table->foreignId("selection_id")->comment("گزینش");
            $table->integer("minimum_score_to_confirm_selection")->comment("حداقل امتیاز برای تایید مصاحبه");
            $table->integer("priority_number")->comment("اولویت");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `selection_post_settings` comment 'جدول تنظیمات پست گزینش'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selection_post_settings');
    }
}
