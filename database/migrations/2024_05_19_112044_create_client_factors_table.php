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
        Schema::create('client_factors', function (Blueprint $table) {
            $table->id();
            $table->string("caption")->comment('شرح کالا');
            $table->foreignId("client_factor_type_id")->comment('نوع فاکتور');
            $table->string("code")->nullable()->comment('  شماره فاکتور');
            $table->string("sum_amount")->comment('جمع کل فاکتور');
            $table->string("service_code")->nullable()->comment('کد کالا /خدمت');
            $table->string("total_amount")->nullable()->comment(' پزداخت با هزینه مالیات جمع کل');
            $table->string("tax")->nullable()->comment('مالیات');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_factors');
    }
};
