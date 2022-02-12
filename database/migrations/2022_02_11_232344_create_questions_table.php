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
        Schema::create('questions', function (Blueprint $table) {
            $table->bigIncrements('cod_question')->comment('question identifier');
            $table->string('name','100')->comment('name question');
            $table->text('question')->comment('question text');
            $table->boolean('required')->default(1)->comment('if the question requires a mandatory answer');
            $table->text('image')->nullable()->comment('image url, in case the question requires it');
            $table->enum('type',['short_answer','long_text','multiple_choice','checkboxes','dropdown','date','time','datetime','numerical'])->comment('type of question');
            $table->timestamps();
            $table->unsignedBigInteger('cod_survey')->comment('survey identifier');
            $table->foreign('cod_survey')
                ->references('cod_survey')
                ->on('surveys')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
