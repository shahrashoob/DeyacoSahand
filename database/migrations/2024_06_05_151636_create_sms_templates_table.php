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
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string("caption")->nullable()->comment("نام قالب");
            $table->text("text")->nullable()->comment("متن پیامک");
            $table->foreignId("sms_template_group_id")->nullable()->comment("متن پیامک");
            $table->foreignId("is_force")->default(0)->comment("ارسال پیامک بدون در نظر گرفتن اعتبار ");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_templates');
    }
};
