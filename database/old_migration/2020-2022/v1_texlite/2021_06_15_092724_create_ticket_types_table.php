<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("caption");
            $table->text("description");
            $table->foreignId("first_post_id")->nullable()->references("id")->on("posts");
            $table->boolean("has_machine")->default(false)->comment("آیا این نوع تیکت نیاز به ثبت ماشین دارد؟");
            $table->boolean("has_ic")->default(false)->comment("آیا این نوع تیکت نیاز به ثبت مرکز هزینه دارد؟");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_types');
    }
}
