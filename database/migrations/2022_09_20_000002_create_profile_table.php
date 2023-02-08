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

            $table
                ->bigInteger('id')
                ->primary();

            $table
                ->string('password', 256)
                ->index('profile_password')
                ->comment('Hash: sha256');

            $table
                ->string('email')
                ->unique()
                ->index('profile_email');

            $table
                ->rememberToken()
                ->nullable()
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
