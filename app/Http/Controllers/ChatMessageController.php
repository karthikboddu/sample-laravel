<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Product;
use App\ChatMessage;
use App\Channel;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Events\MessageSent;

class ChatMessageController extends Controller
{
    //
	protected $user;
	protected $channel;
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
            ->get(['id','user_id', 'receiver_id', 'message'])
            ->toArray();

	}


	public function getAllUserChatMessages(Request $request){
	$receiver_id = $request->receiver_id;
	//return  ChatMessage::select('id','user_id', 'receiver_id', 'message')
                //           ->where([['user_id','=',$this->user->id],['receiver_id','=', $receiver_id]])
                   //        ->get();
return ChatMessage::All();


	}


    public function store(Request $request)
    {


        //return $this->user->products;
        $conversations = new ChatMessage();
	$user_id = (string)$this->user->id;
	$conversations->user_id =  $user_id;
	$conversations->receiver_id = (string)$request->receiver_id;
	$conversations->author_username = $request->author_username;
        //$conversations->channel_id = $request->channel_id;
        $conversations->message = $request->message;
        //$message->quantity = $request->quantity;
    	//$user = User::join('chat_messages', 'chat_messages.user_id', '=', 'users.id')
       //->select('chat_messages.*')->where('chat_messages.user_id', '=', $this->user->id)
       //->get();

        if ($this->user->chatMessage()->save($conversations)){
	    event(new MessageSent("message", $conversations));

		//if($eachConv['receiver_id'] == $id){
		$fromUsers = User::find($conversations->user_id);
		$toUsers = User::find($conversations->receiver_id);
		$conversations['fromUsers'] = $fromUsers;
		$conversations['toUsers'] = $toUsers;

		//$allConservation = $eachConv;
			//}


            return response()->json([
                'success' => true,
                'conversations' => $conversations,
		'user'=>$this->user	
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product could not be added'
            ], 500);
	}
    }


	
    public function ConversationsByUser(Request $request,$id)
    {


        //return $this->user->products;
        $messages = new ChatMessage();
	$this->channel = Channel::find(2);
	$user_id = (string)$this->user->id;
	$conservations = $this->channel->channelHasChatMessage()->get('*')->toArray();
	//$conservations = ChatMessage::select('*')->where([['channel_id','=',2]])->get()->toArray();;
        if ($conservations){
	  $allConservation = array();
	  foreach($conservations as $key => $eachConv){
		$fromUsers[$key] = User::find($eachConv['user_id']);
		$toUsers[$key] = User::find($eachConv['receiver_id']);
		$eachConv['fromUsers'] = $fromUsers[$key];
		$eachConv['toUsers'] = $toUsers[$key];
		$allConservation[$key] = $eachConv;
		}
		//$allConservation['fromUsers'] = $fromUsers;
		//$allConservation['toUsers'] = $toUsers;
		//$allConservation['conversations'] = $eachConservation;
	  return response()->json([
                'success' => 'true',
                'conversations' => $allConservation,
		'fromUsers'=>$this->user->chatMessage()->get(['id','user_id', 'receiver_id', 'message'])
            ->toArray()
            ]);
          
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product could not be added'
            ], 500);
	}
    }


    public function ConversationsChatListByUser(Request $request,$id)
    {


        //return $this->user->products;
        $messages = new ChatMessage();
	//$this->channel = $this->user->channel;
	$user_id = (string)$this->user->id;
	$conservations = $this->user->chatMessage()->get('*')->toArray();
	//$conservations = ChatMessage::select('*')->where([['channel_id','=',2]])->get()->toArray();
	$a = ChatMessage::select('*')
                           ->where([['user_id','=',$user_id],['receiver_id','=', $id]])
                           ->get();
	$b = ChatMessage::select('*')
                           ->where([['user_id','=',$id],['receiver_id','=', $user_id]])
                           ->get();
	$result = $a->merge($b);
        if ($result){
	  $allConservation = array();
	//echo "<pre>";print_r($conservations);exit;
	  foreach($result as $key => $eachConv){
		//if($eachConv['receiver_id'] == $id){
		$fromUsers[$key] = User::find($eachConv['user_id']);
		$toUsers[$key] = User::find($eachConv['receiver_id']);
		$eachConv['fromUsers'] = $fromUsers[$key];
		$eachConv['toUsers'] = $toUsers[$key];

		$allConservation[$key] = $eachConv;
			//}
		}
	$filterConv = array_values(array_filter($allConservation));
	 
	//echo "<pre>";print_r($allConservation);exit;
		//$allConservation['fromUsers'] = $fromUsers;
		//$allConservation['toUsers'] = $toUsers;
		//$allConservation['conversations'] = $eachConservation;
	  return response()->json([
                'success' => 'true',
                'conversations' => $allConservation
            
            ]);
          
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product could not be added'
            ], 500);
	}
    }


    public function getRecentMessagesByUser(Request $request)
    {

        $messages = new ChatMessage();
	//$this->channel = $this->user->channel;
	$user_id = (string)$this->user->id;
	$allUseridConv = array();
	$allRecentUserConv = array();
	$filterRecentConv = array();
	$a = ChatMessage::select('*')
                           ->where([['user_id','=',$user_id]])
                           ->get();
	$b = ChatMessage::select('*')
                           ->where([['receiver_id','=', $user_id]])
                           ->get();
	$result = $a->merge($b);
	$conservations = $this->user->chatMessage()->get('receiver_id')->toArray();
 	foreach($result as $key => $eachConv){
		if(!($eachConv['receiver_id'] == $user_id)){
			$allUseridConv[$key] = $eachConv['receiver_id'];		
		}
		
	}
	$unqUserId = array_unique($allUseridConv);
	foreach($unqUserId as $key => $eachunqUserId){
		$recentConv[$key] = $this->getLastMessages($eachunqUserId);
		}
	if(!empty($recentConv)){
	$filterRecentConv = array_values(array_filter($recentConv));
	}
	//echo "<pre>";print_r(array_values(array_filter($recentConv)));exit;
	foreach($filterRecentConv as $key => $eachConv){

		$toUsers[$key] = User::find($eachConv['receiver_id']);

		$eachConv['receiverUsers'] = $toUsers[$key];

		$allRecentUserConv[$key] = $eachConv;
	}
	//echo "<pre>";print_r($allRecentUserConv);exit;	
	return response()->json([
                'success' => 'true',
                'conversations' => $allRecentUserConv,
		'result'=>$result
            
            ]);
	

   }



   public function getLastMessages($conservations){
			return ChatMessage::select('id','user_id', 'receiver_id', 'message')
                           ->where('receiver_id', $conservations)
                           ->latest('id')->first()->toArray();
	
	


   }











}
