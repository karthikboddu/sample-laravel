<?php

namespace App;
use App\User;
use App\Channel;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
     public $fillable = ['user_id','receiver_id', 'message'];


    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function channels()
    {
    	return $this->belongsTo(Channel::class);
    }
}
