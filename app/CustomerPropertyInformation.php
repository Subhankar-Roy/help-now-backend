<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPropertyInformation extends Model
{
	protected $table = 'helpnow_customer_property_informations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];
    
    protected $dates = ['deleted_at'];
}
