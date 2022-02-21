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
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id_file')->comment('file identifier');
            $table->string('path')->comment('file route');
            $table->string('name',100)->comment("file name");
            $table->string('extension',5)->comment('file extension');
            $table->string('type');
            $table->unsignedBigInteger('id_user')->comment('user identifier');
            $table->timestamps();
            $table->foreign('id_user')
                ->references('id')
                ->on('users')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
