<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    //
    public function simulation_data()
    {
        return $this->belongsTo(Simulation::class,'simulation_id','id');

    }
}
