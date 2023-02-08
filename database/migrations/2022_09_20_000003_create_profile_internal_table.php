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
        Schema::create('profile_internal', function (Blueprint $table) {
            $table->bigInteger('profile_id');

            $table->foreign('profile_id')
                ->references('id')
                ->on('profile');

            $table
                ->string('internal_id')
                ->primary();

            $table->string('internal_code');

            $table
                ->string('company')
                ->nullable();

            $table
                ->string('user_phone')
                ->nullable();

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
        Schema::dropIfExists('profile_internal');
    }
};
