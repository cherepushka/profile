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
            $table->foreign('profile_id')
                ->references('profile_id')->on('profile_internal')
                ->onDelete('cascade');

            $table->bigInteger('profile_id')->primary();
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
        Schema::dropIfExists('profile');
    }
};
