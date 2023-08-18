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
        Schema::create('invoice_custom_values', function (Blueprint $table) {
            $table->string('order_id')
                ->primary();

            $table->foreign('order_id')
                ->references('order_id')->on('invoice')
                ->onDelete('cascade');

            $table->text('web_value')->nullable();
            $table->timestamp('web_value-updated_at')->nullable();

            $table->text('1C_value')->nullable();
            $table->timestamp('1C_value-updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_custom_values');
    }
};
