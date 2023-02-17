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
        Schema::create('profile', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);

            $table->string('password', 256)->comment('Hash: sha256');

            $table->string('email', 256);

            $table->string('phone', 256)
                ->comment('Hash: sha256');

            $table->rememberToken()
                ->nullable()
                ->index('profile_remember_token');

            $table->integer('auth_sms_code')
                ->nullable()
                ->unsigned();

            $table->timestamps();

            $table->unique(['email', 'phone'], 'email_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile');
    }
};
