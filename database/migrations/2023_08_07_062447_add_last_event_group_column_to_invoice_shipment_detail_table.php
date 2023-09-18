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
            $table->string('last_event_group')->nullable()->after('facture_date');
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
            $table->dropColumn('last_event_group');
        });
    }
};
