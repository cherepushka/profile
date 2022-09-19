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
        Schema::create('profiles', function (Blueprint $table) {
            $table->string('internal_id')->unique();
            $table->integer('internal_code');
            $table->string('password', 64)->comment('Hash: sha256');
            $table->string('phone', 64)->comment('Hash: sha256');
            $table->string('email')->unique();
            $table->string('company')->comment('Название контрагента');
            $table->rememberToken();
            $table->dateTime('registration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};
