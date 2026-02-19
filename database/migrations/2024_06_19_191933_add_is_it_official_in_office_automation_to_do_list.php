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
        Schema::table('office_automation_to_do_list', function (Blueprint $table) {
            $table->integer('is_it_official')->default(0)->comment('ایا به صورت رسمس است');
            $table->text('description_official')->nullable()->comment('متن  رسمی');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_automation_to_do_list', function (Blueprint $table) {
            //
        });
    }
};
