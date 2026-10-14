<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    protected $table = 'simulations';

    protected $primaryKey = 'id';

    public function tokens_getter()
    {
        return $this->hasMany(Token::class,'simulation_id');
    }

    public function chat_users()
    {
        return $this->hasMany(ChatUser::class,'simulation_id','id');

    }
    public function create_user()
    {
        return $this->belongsTo(User::class,'created_by','id');

    }
}






