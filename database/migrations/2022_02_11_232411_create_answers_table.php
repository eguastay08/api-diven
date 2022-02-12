<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id()->comment('Identifier answer');
            $table->bigInteger('cod_answer')->comment('Response Group Identifier');
            $table->unsignedBigInteger('cod_question')->comment('Identifier question');
            $table->unsignedBigInteger('cod_option')->nullable()->comment('Identifier option');
            $table->text('answer_txt')->nullable()->comment('Response in case the type is text');
            $table->bigInteger('latitude')->comment('latitude where the response was recorded');
            $table->bigInteger('length')->comment('length where the response was recorded');
            $table->unsignedBigInteger('id_user')->comment('identifier user');
            $table->timestamps();
            $table->foreign('id_user')
                ->references('id')
                ->on('users')->cascadeOnUpdate();

            $table->foreign('cod_question')
                ->references('cod_question')
                ->on('questions')->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('cod_option')
                ->references('cod_option')
                ->on('options')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
};
