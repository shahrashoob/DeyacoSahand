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
        Schema::table('packing_type_label_printing_types', function (Blueprint $table) {
            //
            $table->string('caption2')->comment("نام مستعار نوع بسته بندی")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_type_label_printing_types', function (Blueprint $table) {
            //
        });
    }
};
