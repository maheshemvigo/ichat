<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'tokens';
    
    protected $primaryKey = 'id';

    public function simulation()
    {
        return $this->belongsTo(Simulation::class, 'simulation_id');
    }
}
