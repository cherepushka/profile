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
        Schema::create('invoice_payment_item', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);

            $table
                ->string('order_id')
                ->index('inv_pay_items_order_id');

            $table
                ->foreign('order_id')
                ->references('order_id')
                ->on('invoice_payment')
                ->onDelete('cascade');

            $table
                ->double('amount', 12, 2)
                ->comment('Piece of paid from total payment');

            $table
                ->integer('percent')
                ->comment('Piece of percent from total payment');

            $table->dateTime('payment_date');
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
        Schema::dropIfExists('invoice_payment_item');
    }
};
