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
        Schema::table('invoice_shipment_detail', function (Blueprint $table) {
            $table->string('facture_number')->nullable();
            $table->dateTime('facture_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_shipment_detail', function (Blueprint $table) {
            $table->dropColumn('facture_number');
            $table->dropColumn('facture_date');
        });
    }
};
