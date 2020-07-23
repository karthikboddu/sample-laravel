<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
     public $fillable = ['user_id','sender_id', 'message'];


    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
