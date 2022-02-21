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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment("user identifier");
            $table->string('name')->comment('name of the users');
            $table->string('lastname')->comment('surname of the users');
            $table->string('email')->unique()->comment("users mail ");
            $table->enum('gender',['male','female','other'])->nullable()->comment('gender of users');
            $table->string('password')->nullable()->comment('users password');
            $table->text('photography')->nullable()->comment('user photography');
            $table->dateTime('email_verified_at')->nullable()->comment('email date verify');
            $table->boolean('active')->default(1)->comment('User is active');
            $table->string('google_id')->unique()->nullable()->comment('user google Id');
            $table->rememberToken();
            $table->timestamps();
            /*foreign keys*/
            $table->unsignedBigInteger('cod_rol')->comment('user role');
            $table->foreign('cod_rol')
                ->references('cod_rol')
                ->on('roles')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
