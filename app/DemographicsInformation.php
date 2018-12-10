<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DemographicsInformation extends Model
{
    protected $table = 'helpnow_demographics_informations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];

}
