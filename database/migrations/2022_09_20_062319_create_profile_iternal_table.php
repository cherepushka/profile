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
        Schema::create('profile_internals', function (Blueprint $table) {
            $table->foreign('profile_id')
                ->references('profile_id')->on('invoices')
                ->onDelete('cascade');

            $table->bigInteger('profile_id')
                ->primary();
            $table->integer('internal_id')
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
        Schema::dropIfExists('profile_internals');
    }
};
