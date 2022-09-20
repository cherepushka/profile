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
        Schema::create('invoice', function (Blueprint $table) {

            $table->string('order_id')
                ->primary()
                ->index('inv_order_id');
            $table->string('invoice_id')
                ->index('inv_invoice_id');
            $table->bigInteger('profile_id')
                ->unique();
            $table->string('pay_link');
            $table->tinyInteger('entity');
            $table->string('responsible_email')->comment('Email менеджера, ответственного за заказ')
                ->unique();
            $table->boolean('pay_block');
            $table->text('custom_field')->nullable();
            $table->dateTime('contract_date');
            $table->string('currency');
            $table->double('order_amount', 12, 2);
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
