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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->index('user_type_id');
            $table->index('mobile_country_id');
            $table->index('country_id');
            $table->index('default_printer_id');
            $table->index('entry_permit_status_id');
            $table->index('exit_permit_status_id');
            $table->index('status_id');
            $table->index('register_status_id');
            $table->index('image_id');
            $table->index('cooperation_type_id');
            $table->index('default_label_printer_id');
            $table->index('country_of_nationality_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
