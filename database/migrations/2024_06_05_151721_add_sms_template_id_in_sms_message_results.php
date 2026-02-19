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
            $table->foreignId("sms_template_id")->after("client_transaction_id")->nullable()->comment("قالب پیامک");
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
