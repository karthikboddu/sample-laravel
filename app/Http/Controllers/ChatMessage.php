<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Product;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
class ChatMessage extends Controller
{
    //
	protected $user;

	public function __construct(){
	    try {
             $this->user = JWTAuth::parseToken()->authenticate();
        	} catch (Exception $e) {
            	return response()->json(['status' => 'Authorization Token not found']);
        	}
	
	}

	public function ChatMessageByUser(){

	   return $this->user
            ->chatMessage()
            ->get(['id','user_id', 'sender_id', 'message'])
            ->toArray();

	}
}
