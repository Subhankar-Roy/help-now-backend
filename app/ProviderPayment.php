<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderPayment extends Model
{
     protected $table = 'helpnow_provider_payment_settings';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];
}
