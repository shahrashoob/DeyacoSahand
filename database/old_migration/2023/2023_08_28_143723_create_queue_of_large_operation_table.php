<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueOfLargeOperationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_of_large_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("large_operation_type_id")->default(1);
            $table->foreignId("status_id")->comment("status type: 3500");
            $table->longText("data");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `queue_of_large_operations` comment 'جدول صف انجام عملیات های بزرگ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue_of_large_operation');
    }
}
