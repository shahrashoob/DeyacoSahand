<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostEntryStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_entry_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id");
            $table->foreignId("status_id");
            $table->integer("allow_show_personal_menu");
            $table->integer("allow_show_all_menu");
        });

        DB::statement("ALTER TABLE `post_entry_status` comment 'در این جدول وضعیت مشاهده صفحه پرسنلی/تمامی منوها برای هر پست ذخیره می گردد. اولویت مشاهده صفحه پرسنلی بیشتر می باشد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_entry_status');
    }
}
