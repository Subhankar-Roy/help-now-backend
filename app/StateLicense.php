<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StateLicense extends Model
{
	protected $table = 'helpnow_state_license';

	public function statesinfo()
    {
        return $this->belongsTo('App\State', 'state_id', 'id');
    }
    
}
