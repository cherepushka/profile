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
        Schema::create('invoice_shipment_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);

            $table
                ->string('order_id')
                ->index('inv_shp_det_order_id');

            $table
                ->foreign('order_id')
                ->references('order_id')
                ->on('invoice_shipment')
                ->onDelete('cascade');

            $table->string('realization_id');
            $table->integer('realization_number');
            $table->double('amount', 12, 2);
            $table->string('transport_company');
            $table->integer('transport_company_id');
            $table->dateTime('date');
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
        Schema::dropIfExists('invoice_shipment_detail');
    }
};
