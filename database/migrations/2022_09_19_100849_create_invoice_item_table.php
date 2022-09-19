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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('vendor_code');
            $table->string('internal_id');
            $table->string('title');
            $table->string('category');
            $table->string('unit');
            $table->integer('quantity');
            $table->double('pure_price');
            $table->string('VAT_rate');
            $table->double('VAT_sum');
            $table->double('final_price');
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
