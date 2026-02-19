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
        Schema::create('warehouse_shelving_types', function (Blueprint $table) {
            $table->id();
            $table->string('caption')->comment("عنوان طبقه بندی");
            $table->string('sub_caption')->comment("عنوان زیر طبقه بندی");
            $table->integer("priority_number")->comment("اولویت");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_shelving_types');
    }
};
