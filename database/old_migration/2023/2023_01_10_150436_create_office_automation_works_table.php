<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeAutomationWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_automation_works', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("caption");
            $table->foreignId("user_id");
            $table->foreignId("status_id");
            $table->foreignId("parent_id")->nullable()->comment("ممکن است که یک نامه حاصل ارجاع نامه دیگری باشد، انگاه یک ارتباط ایجاد می شود");
            $table->longText("description")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_automation_letters');
    }
}
