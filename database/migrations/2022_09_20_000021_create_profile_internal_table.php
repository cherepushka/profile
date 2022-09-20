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
            $table->foreign('internal_id')
                ->references('profile_id')->on('invoice')
                ->onDelete('cascade');

            $table->bigInteger('profile_id')
                ->unique();
            $table->bigInteger('internal_id')
                ->unique();
            $table->integer('internal_code');
            $table->string('company')->comment('Название контрагента');
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
