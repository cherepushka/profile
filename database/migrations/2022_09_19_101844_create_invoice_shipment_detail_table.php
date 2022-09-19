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
        Schema::create('invoice_shipment_details', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('realization_id');
            $table->string('realization_number');
            $table->integer('amount');
            $table->string('transport_company');
            $table->integer('transport_id');
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
