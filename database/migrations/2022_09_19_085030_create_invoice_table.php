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
        Schema::create('invoices', function (Blueprint $table) {
            $table->string('order_id');
            $table->string('invoice_id');
            $table->bigInteger('profile_id');
            $table->string('pay_link');
            $table->tinyInteger('entity');
            $table->string('responsible_email')->comment('имейл менеджера, ответственного за заказ');
            $table->tinyInteger('pay_block');
            $table->text('custom_field')->nullable();
            $table->dateTime('contract_date');
            $table->string('currency');
            $table->double('order_amount');
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
        Schema::dropIfExists('invoice');
    }
};
