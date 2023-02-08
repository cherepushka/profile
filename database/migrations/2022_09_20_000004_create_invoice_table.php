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
            $table
                ->string('order_id')
                ->primary()
                ->index('inv_order_id');

            $table
                ->string('invoice_id')
                ->index('inv_invoice_id');

            $table->string('user_id');

            $table
                ->foreign('user_id')
                ->references('internal_id')
                ->on('profile_internal');

            $table->tinyInteger('entity');
            $table->string('responsible_email');

            $table
                ->foreign('responsible_email')
                ->references('email')
                ->on('manager');

            $table->text('pay_link');
            $table->boolean('pay_block');

            $table
                ->text('custom_field')
                ->nullable();

            $table
                ->dateTime('contract_date')
                ->nullable();

            $table->string('currency');
            $table->double('order_amount', 12, 2);

            $table
                ->string('roistat_id')
                ->nullable();

            $table
                ->string('deal_source')
                ->nullable();

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
