<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpireLicense extends Model
{
    protected $table = 'helpnow_expire_licenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id'
    ];
}
