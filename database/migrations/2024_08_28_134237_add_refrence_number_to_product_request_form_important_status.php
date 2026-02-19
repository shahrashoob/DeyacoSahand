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
        Schema::table('product_request_form_important_status', function (Blueprint $table) {
            //
            $table->string('reference_number')->nullable()->after('applicant_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_form_important_status', function (Blueprint $table) {
            //
        });
    }
};
