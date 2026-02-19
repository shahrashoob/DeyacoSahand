<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostSmartObjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_smart_object', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id");
            $table->foreignId("smart_object_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `post_smart_object` comment 'به ازای هر پست یک یا چند شیء هوشمند داریم.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_smart_option');
    }
}
