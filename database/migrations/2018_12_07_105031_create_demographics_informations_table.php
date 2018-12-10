<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemographicsInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helpnow_demographics_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('user_id');
            $table->string('language')->nullable();
            $table->enum('gender',[1,2,3])->comment('1-> Male, 2-> Female, 3-> Others')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('ethnicity')->nullable();
            $table->enum('relationship',[1,2,3,4])->comment('1->Unmarried, 2-> Married, 3-> Divorced, 4->Widow')->nullable();
            $table->string('education')->nullable();
            $table->string('occupation')->nullable();
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
        Schema::dropIfExists('helpnow_demographics_informations');
    }
}