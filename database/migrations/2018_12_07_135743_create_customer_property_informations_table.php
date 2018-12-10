<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerPropertyInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helpnow_customer_property_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('user_id');
            $table->string('name');
            $table->enum('property_type',[1,2,3,4])->comment('1->Residential, 2-> Commercial, 3-> Industrial, 4->Agricultural');
            $table->string('street')->nullable();
            $table->string('po')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('area')->nullable();
            $table->string('area_unit')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('helpnow_customer_property_informations');
    }
}
