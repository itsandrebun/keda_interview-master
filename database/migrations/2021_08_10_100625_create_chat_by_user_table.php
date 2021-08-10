<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatByUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_per_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('first_person_id')->nullable();
            $table->unsignedBigInteger('second_person_id')->nullable();
            $table->unsignedBigInteger('last_chat_id')->nullable();
            $table->timestamps();
            $table->foreign('first_person_id')->references('user_id')->on('users')->onUpdate('NO ACTION')->onDelete('cascade');
            $table->foreign('second_person_id')->references('user_id')->on('users')->onUpdate('NO ACTION')->onDelete('cascade');
            $table->foreign('last_chat_id')->references('id')->on('chats')->onUpdate('NO ACTION')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_by_user');
    }
}
