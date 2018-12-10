<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPaymentSettings extends Model
{
    protected $table = 'helpnow_customer_payment_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];
}
