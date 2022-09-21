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
        Schema::create('manager', function (Blueprint $table) {

            $table->id();
            $table->string('name',50);
            $table->string('surname', 50);
            $table->string('position');
            $table->string('email')
                ->unique()
                ->index('manager_email');
            $table->string('phone', 16);
            $table->string('whats_app', 16)->nullable();
            $table->string('image')->nullable()->comment('Manager image URL');
            $table->boolean('status');
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
        Schema::dropIfExists('manager');
    }
};
