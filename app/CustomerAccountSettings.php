<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAccountSettings extends Model
{
    protected $table = 'helpnow_customer_account_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];
}
