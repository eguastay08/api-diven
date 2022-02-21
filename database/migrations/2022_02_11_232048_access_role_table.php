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
        Schema::create('access_role', function (Blueprint $table) {
            $table->unsignedBigInteger('cod_access')->comment('access identifier');
            $table->unsignedBigInteger('cod_rol')->comment('role identifier');
            $table->timestamps();
            $table->primary(['cod_rol','cod_access']);
            $table->foreign('cod_rol')
                ->references('cod_rol')
                ->on('roles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('cod_access')
                ->references('cod_access')
                ->on('access')->cascadeOnUpdate()->cascadeOnDelete();
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
