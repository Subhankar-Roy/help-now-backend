<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->enum('user_type', [1,2,3,4,5,6])->comment('1-> superadmin, 2-> admin, 3-> registered customer, 4-> non-registered customer, 5-> service provider, 6-> technician');
            $table->enum('registration_type', [1,2,3,4,5,6])->comment('1-> email-password login, 2->google login, 3-> facebook login, 4-> twitter login, 5-> instagram login, 6-> snapchat login');
            $table->string('registration_token')->comment('Social login authentication token')->nullable();
            $table->string('deleted_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('hlpnw_users');
    }
}
