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
        Schema::table('sms_message_results', function (Blueprint $table) {
            $table->foreignId("client_transaction_id")->after("cost")->nullable()->comment("تراکنش");
            $table->integer("cost_with_coefficient")->after("cost")->nullable()->comment("هزینه پیامک با ضریب.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_message_results', function (Blueprint $table) {
            //
        });
    }
};
