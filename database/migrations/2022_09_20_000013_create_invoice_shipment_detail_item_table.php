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
//            $table->foreign('invoice_product_id')
//                ->references('id')->on('invoice_item')
//                ->onDelete('cascade');

            $table->foreign('order_id')
                ->references('order_id')->on('invoice_shipment_detail')
                ->onDelete('cascade');

            $table->id();
            $table->string('order_id')
                ->unique()
                ->index('inv_sh_det_item_order_id');
            $table->integer('invoice_product_id')
                ->unique();
            $table->integer('product_quantity');
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