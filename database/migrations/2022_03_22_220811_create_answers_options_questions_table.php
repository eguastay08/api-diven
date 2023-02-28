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
        Schema::create('answers_options_questions', function (Blueprint $table) {
            $table->id()->comment('Identifier answers options questions');
            $table->unsignedBigInteger('cod_question')->comment('Identifier question');
            $table->unsignedBigInteger('cod_option')->nullable()->comment('Identifier option');
            $table->text('answer_txt')->nullable()->comment('Response in case the type is text');
            $table->unsignedBigInteger('id_file')->nullable()->comment('Identifier file');
            $table->unsignedBigInteger('cod_answer')->comment('identifier answer');
            $table->timestamps();

            $table->foreign('cod_question')
            ->references('cod_question')
            ->on('questions')->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign(['cod_question','cod_option'])
                ->references(['cod_question','cod_option'])
                ->on('options')->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('cod_answer')
                ->references('cod_answer')
                ->on('answers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('id_file')
                ->references('id_file')
                ->on('files')->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique(['cod_question','cod_option','cod_answer'],'unique_cod_answer');
            $table->unique(['cod_question','cod_option','answer_txt'],'unique_answer_txt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers_options_questions');
    }
};
