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
        Schema::table('post_document_receive_step_confirms', function (Blueprint $table) {
            //
            $table->index('receive_document_step_id',"receive_document_step_id");
            $table->index('post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_document_receive_step_confirms', function (Blueprint $table) {
            //
        });
    }
};
