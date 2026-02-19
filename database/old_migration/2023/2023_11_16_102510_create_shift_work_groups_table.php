<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftWorkGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_work_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId("shift_id");
            $table->foreignId("shift_work_id");
            $table->foreignId("shift_work_group_type_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `shift_work_groups` comment 'در بعضی از شیفت ها، دو یا چند گروه شیفت با هم هستند، یعنی اگر فردی در گروه شیف 1 باشد باید در گروه شیفت 3 هم باشد.، این اطلاعات در این جدول ذخیره می گردد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_work_groups');
    }
}
