<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded=[];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function chat_users_data()
    {
        return $this->belongsTo(ChatUser::class,'admin_chat_id','id');

    }
    public function user_data()
    {
        return $this->belongsTo(User::class,'user_id','id');

    }
    public function upload_data()
    {
        return $this->belongsTo(Upload::class,'upload_id','id');

    }
}
