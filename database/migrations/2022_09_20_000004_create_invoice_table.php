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

            $table->string('user_id');
            $table->foreign('user_id')
                ->references('internal_id')->on('profile_internal');

            $table->string('pay_link');
            $table->tinyInteger('entity');

            $table->string('responsible_email');
            $table->foreign('responsible_email')
                ->references('email')->on('manager');

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
