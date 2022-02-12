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
        Schema::create('project_user', function (Blueprint $table) {
            $table->unsignedBigInteger('project_cod_project')->comment('project identifier');
            $table->unsignedBigInteger('user_id')->comment("user identifier");
            $table->primary(['project_cod_project','user_id']);
            $table->timestamps();
            $table->foreign('project_cod_project')
                ->references('cod_project')
                ->on('projects')->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
