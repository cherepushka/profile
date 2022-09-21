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

            $table->unsignedBigInteger('profile_id');
            $table->foreign('profile_id')
                ->references('id')->on('profile');

            $table->string('internal_id')
                ->unique();
            $table->integer('internal_code');
            $table->string('company')->comment('Название контрагента');
            $table->timestamps();
            $table->primary(['profile_id', 'internal_code']);
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
