<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_chanel_next', function (Blueprint $table) {
            $table->id();
            $table->foreignId("production_channel_type_id")->index();
            $table->foreignId("next_production_channel_type_id")->index();
            $table->integer("priority_number")->index();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `production_chanel_next` comment 'بعد از هر کانال تولید چه کانال های دیگری می تواند قرار بگیرد'");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_chanel_next');
    }
};
