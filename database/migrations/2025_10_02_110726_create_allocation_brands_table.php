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
        Schema::create('allocation_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allocation_id')->index();
            $table->foreignId('allocation_doff_id')->index();
            $table->integer('doff_number')->comment("شماره داف");
            $table->double('amount_of_brand')->comment("مقدار برند (واحد اصلی کالا)");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `allocation_brands` comment 'تعداد برند که به ازای هر داف باید چاپ شود'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_brands');
    }
};
