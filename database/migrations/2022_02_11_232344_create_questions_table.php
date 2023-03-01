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
            $table->string('name','100')->nullable()->comment('name question');
            $table->text('question')->nullable()->comment('question text');
            $table->boolean('required')->default(false)->comment('if the question requires a mandatory answer');
            $table->text('image')->nullable()->comment('image url, in case the question requires it');
            $table->enum('type',['short_answer','long_text','multiple_choice','checkboxes','dropdown','date','time','datetime','numerical','image'])->comment('type of question');
            $table->bigInteger('order')->comment('order questions');
            $table->timestamps();
            $table->unsignedBigInteger('cod_section')->comment('section identifier');
            $table->foreign('cod_section')
                ->references('cod_section')
                ->on('sections')->cascadeOnUpdate()->cascadeOnDelete();
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
