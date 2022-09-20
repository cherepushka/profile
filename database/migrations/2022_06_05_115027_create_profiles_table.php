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
            $table->foreign('id')
                ->references('profile_id')->on('profile_internals')
                ->onDelete('cascade');

            $table->id();
            $table->string('password', 64)->comment('Hash: sha256')
                ->index('profile_password');
            $table->string('phone', 64)->comment('Hash: sha256')
                ->unique()
                ->index('profile_phone');
            $table->string('email')
                ->unique()
                ->index('profile_email');
            $table->rememberToken()
                ->index('profile_token');
            $table->string('status');
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
