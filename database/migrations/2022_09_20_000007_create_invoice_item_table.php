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
        Schema::create('invoice_item', function (Blueprint $table) {
            $table
                ->bigInteger('id')
                ->primary();

            $table
                ->string('order_id')
                ->index('inv_item_order_id');

            $table
                ->foreign('order_id')
                ->references('order_id')
                ->on('invoice')
                ->onDelete('cascade');

            $table
                ->string('vendor_code')
                ->index('inv_item_vendor_code');

            $table
                ->string('internal_id')
                ->index('inv_item_internal_id');

            $table->string('title');
            $table->string('category');
            $table->string('unit');
            $table->integer('qty');
            $table->double('pure_price', 12, 2);
            $table->integer('VAT_rate');
            $table->double('VAT_sum', 12, 2);
            $table->double('final_price', 12, 2);
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
        Schema::dropIfExists('invoice_item');
    }
};
