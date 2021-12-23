<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
public $fillable = ['user_id','receiver_id', 'message'];
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

}
