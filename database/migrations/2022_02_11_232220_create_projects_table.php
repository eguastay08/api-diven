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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('cod_project')->comment('project identifier');
            $table->text('name')->comment('name project');
            $table->string('resolution',50)->comment('resolution project hcu');
            $table->text('detail')->nullable()->comment('detail project');
            $table->text('image')->nullable()->comment('image project');
            $table->unsignedBigInteger('cod_dpa')->comment('dpa identifier');
            $table->timestamps();

            $table->foreign('cod_dpa')
                ->references('cod_dpa')
                ->on('dpas')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
