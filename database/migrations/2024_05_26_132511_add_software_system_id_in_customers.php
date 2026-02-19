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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('software_system_id')->nullable()->comment("نام سامانه جامع ");
            $table->string('api_url')->nullable()->comment("آدرس سامانه جامع");
            $table->string('api_username')->nullable()->comment("نام کاربری");
            $table->string('api_password')->nullable()->comment("رمز عبور");
            $table->string('api_key')->nullable()->comment("api_key");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
