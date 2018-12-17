<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('helpnow_provider_payment_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('user_id');
            $table->string('bank')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing')->nullable();
            $table->string('name_on_card')->nullable();
            $table->string('street')->nullable();
            $table->string('po')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('paypal_account')->nullable();
            $table->string('venmo_account')->nullable();
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
        Schema::dropIfExists('helpnow_provider_payment_settings');
    }
}
