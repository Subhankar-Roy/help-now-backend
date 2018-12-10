<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfessionalInformation extends Model
{
    protected $table = 'helpnow_professional_informations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];
}
