<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerAccountSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helpnow_customer_account_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('user_id');
            $table->integer('settings_name')->comment('1->Notification, 2->Special Offers, 3->Privacy, 4->Posts, 5->Status, 6->Service');
            $table->string('settings')->comment('T->Text, E->Email, S->Social, P->Phone, 0->Inactive, 1->Active');
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
        Schema::dropIfExists('helpnow_customer_account_settings');
    }
}
