<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderOrganization extends Model
{
    use SoftDeletes;
    /**
     * Custom table name with a prefix
     * 
     * @var string
     */
    protected $table = 'helpnow_provider_organizations';
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
