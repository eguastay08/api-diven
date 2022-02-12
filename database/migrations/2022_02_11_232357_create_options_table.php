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
        Schema::create('options', function (Blueprint $table) {
            $table->bigIncrements('cod_option')->comment('option identifier');
            $table->text('option')->nullable()->comment('question option');
            $table->text('image')->nullable()->comment('image url, in case the option requires it');
            $table->timestamps();
            $table->unsignedBigInteger('cod_question')->comment('survey identifier');
            $table->foreign('cod_question')
                ->references('cod_question')
                ->on('questions')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
};
