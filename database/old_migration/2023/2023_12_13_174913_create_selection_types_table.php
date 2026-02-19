<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSelectionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_types', function (Blueprint $table) {
            $table->id();
            $table->string('caption')->comment('عنوان');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `selection_types` comment 'جدول انواع گزینش'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selection_types');
    }
}
