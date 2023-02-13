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
        Schema::create('document', function (Blueprint $table) {
            $table->id();

            $table
                ->foreign('order_id')
                ->references('order_id')
                ->on('invoice')
                ->onDelete('cascade');

            $table
                ->string('order_id')
                ->index('doc_order_id');

            $table
                ->string('filename')
                ->index('doc_filename');

            $table->string('extension');
            $table->string('section');
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
        Schema::dropIfExists('document');
    }
};
