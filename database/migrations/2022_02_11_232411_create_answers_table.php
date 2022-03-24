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
            $table->id('cod_answer')->comment('Identifier answer');
            $table->bigInteger('latitude')->comment('latitude where the response was recorded');
            $table->bigInteger('longitude')->comment('length where the response was recorded');
            $table->unsignedBigInteger('id_user')->comment('identifier user');
            $table->unsignedBigInteger('cod_survey')->comment('Identifier survey');
            $table->timestamps();
            $table->foreign('id_user')
                ->references('id')
                ->on('users')->cascadeOnUpdate();

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
        Schema::dropIfExists('answers');
    }
};
