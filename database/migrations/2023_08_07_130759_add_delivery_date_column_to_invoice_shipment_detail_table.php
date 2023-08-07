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
            $table->dateTime('delivery_date')->nullable()->after('last_event_group');
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
            $table->dropColumn('delivery_date');
        });
    }
};
