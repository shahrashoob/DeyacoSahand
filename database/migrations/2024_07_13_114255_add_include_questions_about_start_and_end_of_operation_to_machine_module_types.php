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
        Schema::table('machine_module_types', function (Blueprint $table) {
            //
            $table->integer("include_questions_about_start_and_end_of_operation")->default(0)->
            comment(" شامل سوالات شروع عملیات و پایان عملیات در تعریف مسیر محصول کالا است؟");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_module_types', function (Blueprint $table) {
            //
        });
    }
};
