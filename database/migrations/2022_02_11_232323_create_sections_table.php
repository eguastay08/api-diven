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
        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('cod_section')->comment('Identifier section');
            $table->string('name')->comment('name section');
            $table->text('detail')->nullable()->comment('detail from section');
            $table->bigInteger('order')->comment('order section');
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
        Schema::dropIfExists('sections');
    }
};
