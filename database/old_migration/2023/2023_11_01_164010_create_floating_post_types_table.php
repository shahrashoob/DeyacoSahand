<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFloatingPostTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('floating_post_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `floating_post_types` comment 'عنوان پست های شناور که در زمان ثبت مجوز از آنها استفاده کردیم.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('floating_post_types');
    }
}
