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
        Schema::create('document_change_encryption_queue', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('document_id')->unique();
            $table->foreign('document_id')
                ->references('id')
                ->on('document')
                ->onDelete('cascade');

            $table->string('old_profile_password');

            $table->unsignedBigInteger('profile_id');
            $table->foreign('profile_id')
                ->references('id')
                ->on('profile')
                ->onDelete('cascade');

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
        Schema::dropIfExists('document_change_encryption_queue');
    }
};
