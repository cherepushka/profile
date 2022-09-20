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
        Schema::create('invoice_payment', function (Blueprint $table) {
            $table->foreign('order_id')
                ->references('order_id')->on('invoice')
                ->onDelete('cascade');

            $table->string('order_id')->primary();
            $table->double('paid_amount', 12, 2)->comment('Total paid amount');
            $table->integer('paid_percent')->comment('Total paid percent');
            $table->dateTime('last_payment_date');
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
        Schema::dropIfExists('invoice_payment');
    }
};
