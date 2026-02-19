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
        Schema::create('post_chat_setting', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->index();
            $table->foreignId("chat_with_post_id")->nullable()->index()->comment("پستی که می تواند با آن چت کند");
            $table->integer("chat_with_customers")->nullable()->comment("آیا می تواند یا مشتریان چت کند");
            $table->integer("chat_with_suppliers")->nullable()->comment("آیا می تواند یا تامین کنندگان چت کند");
            $table->integer("chat_with_contractors")->nullable()->comment("آیا می تواند یا پیمانکاران چت کند");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_chat_setting');
    }
};
