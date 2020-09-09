<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Channel;
use App\Message;
use App\Events\MessageSent;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', 'ApiController@login');
Route::post('register', 'ApiController@register');
Route::get('open', 'DataController@open');
 Route::get('/t', function () {
    event(new \App\Events\ChatMessageWasReceived());
    dd('Event Run Successfully.');
});
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('logout', 'ApiController@logout');
 
    //Route::get('user', 'ApiController@getAuthUser');
 	Route::get('user', 'ApiController@getAuthenticatedUser');
	Route::get('getAllUsers', 'ApiController@getAllUsers');
    Route::get('products', 'ProductController@index');
    Route::get('products/{id}', 'ProductController@show');
    Route::post('products', 'ProductController@store');
    Route::put('products/{id}', 'ProductController@update');
    Route::delete('products/{id}', 'ProductController@destroy');
    Route::get('closed', 'DataController@closed');


	//Route::get('conversations/query/{id}', 'ChatMessageController@ConversationsByUser');
    
    Route::get('conversations/query/{id}', 'ChatMessageController@ConversationsChatListByUser');
	Route::get('chatMessages', 'ChatMessageController@ChatMessageByUser');
	Route::get('getAllUserchatMessages', 'ChatMessageController@getAllUserChatMessages');
	Route::post('messages', 'ChatMessageController@store');
	Route::get('conversations/getRecentMessagesByUser', 'ChatMessageController@getRecentMessagesByUser');
});



Route::get('/', function () {
    $channels = Channel::orderBy('name')->get();

    return $channels;
});


Route::get('/channels/{channel}/messages', function (App\Channel $channel) {
    if (!request()->wantsJson()) {
        abort(404);
    }

    $messages = App\Message::where('channel_id', $channel->id)->reorder('id','desc')->get()->toArray();

    return $messages;
});


Route::post('/channels/{channel}/messages', function (App\Channel $channel) {

    $message = Message::forceCreate([
	'user_id'=>'3',
	'sender_id'=>'1',
        'channel_id' => $channel->id,
        'author_username' => request('username'),
        'message' => request('message'),

    ]);

    // Dispatch an event. Will be broadcasted over Redis.
    event(new MessageSent($channel->name, $message));

    return $message;
});





