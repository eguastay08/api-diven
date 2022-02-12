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
        Schema::create('surveys', function (Blueprint $table) {
            $table->bigIncrements('cod_survey')->comment(' survey identifier');
            $table->string('name','100')->comment('survey name');
            $table->dateTime('date_init')->comment('open survey date');
            $table->dateTime('date_finally')->comment('close survey date');
            $table->boolean('status')->default(0)->comment('survey in production or test');
            $table->text('detail')->nullable()->comment('survey detail');
            $table->integer('max_answers')->default(-1)->comment('Maximum number of responses to the survey, by default -1 which is infinite');
            $table->timestamps();

            $table->unsignedBigInteger('cod_project')->comment('project identifier');
            $table->foreign('cod_project')
                ->references('cod_project')
                ->on('projects')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surveys');
    }
};
