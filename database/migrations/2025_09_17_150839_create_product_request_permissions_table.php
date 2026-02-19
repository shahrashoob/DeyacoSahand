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
        Schema::create('product_request_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->nullable()->index();
            $table->string('code')->nullable();
            $table->foreignId('status_id')->default("7015")->index();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `product_request_permissions` comment 'جدول مجوزهای بارگیری'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_request_permissions');
    }
};
