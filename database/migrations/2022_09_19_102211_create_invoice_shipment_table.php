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
        Schema::create('invoice_shipments', function (Blueprint $table) {
            $table->foreign('order_id')
                ->references('order_id')->on('invoices')
                ->onDelete('cascade');

            $table->string('order_id')
                ->primary()
                ->index('inv_shp_order_id');
            $table->string('currency');
            $table->double('amount', 12, 2);
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
        Schema::dropIfExists('invoice_shipments');
    }
};
