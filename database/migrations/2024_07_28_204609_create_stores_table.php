<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('caption')->comment('عنوان');
            $table->text('description')->nullable()->comment('توضیحات خدمت یا کالا');
            $table->string('price')->nullable()->comment('قیمت(ریال|)');
            $table->foreignId('status_id')->default( 4500001)->comment('وضعیت خرید پیش فرض در انتظار خرید');
            $table->foreignId('client_transaction_id')->nullable()->comment('شماره تراکنش');
            $table->foreignId('client_factor_id')->nullable()->comment('شماره فاکتور');
            $table->date('validity_date')->default('2024-07-29')->comment('تاریخ اعتبار برای قیمت');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stors');
    }
};
