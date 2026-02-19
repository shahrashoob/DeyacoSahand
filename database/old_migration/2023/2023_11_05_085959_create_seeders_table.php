<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeedersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seeders', function (Blueprint $table) {
            $table->id();
            $table->string("seeder");
            $table->integer("version")->default(1);
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `seeders` comment 'به ازای هر Seed یک ردیف در این جدول اضافه می گردد و در صورتی که ورژن آن تغییر کرد، Seed اعمال می شود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seeders');
    }
}
