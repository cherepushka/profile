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
        Schema::create('invoice_shipment_detail_item', function (Blueprint $table) {
            $table
                ->bigInteger()
                ->primary();

            $table
                ->string('order_id')
                ->index('inv_sh_det_item_order_id');

            $table
                ->foreign('order_id')
                ->references('order_id')
                ->on('invoice_shipment_detail');

            $table->bigInteger('invoice_product_id');

            $table
                ->foreign('invoice_product_id')
                ->references('id')
                ->on('invoice_item');

            $table->integer('product_qty');
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
        Schema::dropIfExists('invoice_shipment_detail_item');
    }
};
